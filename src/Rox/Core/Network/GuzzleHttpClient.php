<?php

namespace Rox\Core\Network;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;
use Rox\Core\Logging\LoggerFactory;

class GuzzleHttpClient implements HttpClientInterface
{
    /**
     * @var ClientInterface
     */
    private $_client;

    /**
     * @var bool $_logCacheHitsAndMisses
     */
    private $_logCacheHitsAndMisses = false;

    /**
     * @var string[] $_noCachePaths
     */
    private $_noCachePaths = [];

    /**
     * @var LoggerInterface $_log
     */
    private $_log;

    /**
     * GuzzleHttpClient constructor.
     * @param GuzzleHttpClientOptions|null $options
     */
    public function __construct($options = null)
    {
        $config = [];

        if ($options != null) {
            if ($options->isSkipHostKeyVerification()) {
                $config['verify'] = false;
            }
            if ($options->getHandlerStack() != null) {
                $config['handler'] = $options->getHandlerStack();
            }
            $this->_logCacheHitsAndMisses = $options->isLogCacheHitsAndMisses();
            $this->_noCachePaths = $options->getNoCachePaths();
            if ($options->getUserAgent()) {
                $config['headers'] = [
                    'User-Agent' => $options->getUserAgent()
                ];
            }
        }

        $this->_client = new Client($config);
        $this->_log = LoggerFactory::getInstance()->createLogger(self::class);
    }

    /**
     * @param RequestData $requestData
     * @return HttpResponseInterface
     */
    function sendGet(RequestData $requestData)
    {
        $uri = new Uri($requestData->getUrl());
        if ($requestData->getQueryParams() != null) {
            $uri = Uri::withQueryValues($uri, $requestData->getQueryParams());
        }
        try {
            $request = new Request('GET', $uri);
            $noCache = $this->_isNoCacheRequest($uri);
            if ($noCache) {
                // Don't use Kevinrob/GuzzleCache lib constants here to make it optional in composer.json
                $request = $request->withHeader('X-Kevinrob-GuzzleCache-ReValidation', true);
            }
            $response = $this->_client->send($request);
            if (!$noCache && $this->_logCacheHitsAndMisses) {
                $cacheState = $response->getHeader("X-Kevinrob-Cache");
                if (is_array($cacheState) && !empty($cacheState)) {
                    switch ($cacheState[0]) {
                        case 'HIT':
                            $this->_log->debug("{$request->getUri()}: HIT");
                            break;

                        case 'MISS':
                            $this->_log->debug("{$request->getUri()}: MISS");
                            break;
                    }
                }
            }
            return new Psr7ResponseWrapper($response);
        } catch (GuzzleException $e) {
            return $this->_handleError($request->getUri(), $e);
        }
    }

    /**
     * @param RequestData $requestData
     * @return HttpResponseInterface
     */
    function sendPost(RequestData $requestData)
    {
        return $this->postJson($requestData->getUrl(), $requestData->getQueryParams());
    }

    /**
     * @param string $uri
     * @param array $data
     * @return HttpResponseInterface
     */
    function postJson($uri, array $data)
    {
        try {
            return new Psr7ResponseWrapper($this->_client->post(new Uri($uri), [
                RequestOptions::JSON => $data
            ]));
        } catch (GuzzleException $e) {
            $this->_log->error("Failed to send data to ${uri}: {$e->getMessage()}", [
                'exception' => $e
            ]);
            return new HttpErrorResponse($e->getCode(), $e->getMessage());
        }
    }

    /**
     * @param string $uri
     * @param GuzzleException $e
     * @return HttpResponseInterface
     */
    private function _handleError($uri, GuzzleException $e)
    {
        $this->_log->error("Failed to send data to ${uri}: {$e->getMessage()}", [
            'exception' => $e
        ]);
        return new HttpErrorResponse($e->getCode(), $e->getMessage());
    }

    /**
     * @param string $uri
     * @return bool
     */
    private function _isNoCacheRequest($uri)
    {
        return is_array($this->_noCachePaths) &&
            count(array_filter($this->_noCachePaths, function ($path) use ($uri) {
                return strpos($uri, $path) !== false;
            })) > 0;
    }
}
