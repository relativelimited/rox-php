<?php

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\ChainCache;
use Doctrine\Common\Cache\FilesystemCache;
use Kevinrob\GuzzleCache\Storage\DoctrineCacheStorage;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Rox\Core\Consts\Environment;
use Rox\Core\Logging\LoggerFactory;
use Rox\Core\Logging\MonologLoggerFactory;
use Rox\Server\Flags\RoxFlag;
use Rox\Server\Rox;
use Rox\Server\RoxOptions;
use Rox\Server\RoxOptionsBuilder;

const DEFAULT_API_KEY = '5e6a3533d3319d76d1ca33fd';
const DEFAULT_DEV_MODE_KEY = '297c23e7fcb68e54c513dcca';

require __DIR__ . '/vendor/autoload.php';

class Container
{
    public $demoFlag;

    public function __construct()
    {
        $this->demoFlag = new RoxFlag();
    }
}

if (!isset($_ENV[Environment::ENV_VAR_NAME])) {
    $_ENV[Environment::ENV_VAR_NAME] = Environment::QA;
}

$apiKey = isset($_ENV['ROLLOUT_API_KEY'])
    ? $_ENV['ROLLOUT_API_KEY']
    : DEFAULT_API_KEY;

$devModeKey = isset($_ENV['ROLLOUT_DEV_MODE_KEY'])
    ? $_ENV['ROLLOUT_DEV_MODE_KEY']
    : DEFAULT_DEV_MODE_KEY;

$roxOptionsBuilder = (new RoxOptionsBuilder())
    ->setDevModeKey($devModeKey);

echo '<pre>';

if (!isset($_GET['nolog'])) {

    // Example of setting up custom logging handlers. By default it outputs log records
    // to stderr, and default log level is ERROR. Here we're setting custom logging handlers
    // to output to filesystem instead with DEBUG log level.
    //
    // It's also possible to specify custom log record processors by calling
    // MonologLoggerFactory::setDefaultProcessors() function. More info can be found in
    // Monolog documentation https://github.com/Seldaek/monolog/blob/master/doc/01-usage.md.
    //
    // Another option for users is to implement their own Logger Factory and pass it to Rox via
    // LoggerFactory::setup() method.
    //
    // If no logging is needed one can call LoggerFactory::setup(new NullLoggerFactory()).
    //
    // Note LoggerFactory::setup() must be called before Rox::register() and Rox::setup().

    $logFile = join(DIRECTORY_SEPARATOR, [
        sys_get_temp_dir(),
        'rollout',
        'logs',
        'demo.log'
    ]);

    echo "Logging to ${logFile}\n";

    LoggerFactory::setup((new MonologLoggerFactory())
        ->setDefaultHandlers([
            new StreamHandler($logFile, Logger::DEBUG)
        ]));
}

if (!isset($_GET['nocache'])) {

    // Sample caching setup using Doctrine example from https://github.com/Kevinrob/guzzle-cache-middleware

    $roxOptionsBuilder
        ->setCacheStorage(new DoctrineCacheStorage(
            new ChainCache([
                new ArrayCache(),
                new FilesystemCache('/tmp/rollout/cache'),
            ])
        ))
        ->setLogCacheHitsAndMisses(true)
        ->setConfigFetchIntervalInSeconds(30);

    echo "Using cache...\n";

} else {

    echo "NOT using cache...\n";
}

$con = new Container();
Rox::register('demo', $con);
Rox::setup($apiKey, new RoxOptions($roxOptionsBuilder));

if ($con->demoFlag->isEnabled()) {
    echo "demo.demoFlag: FEATURE IS ON\n";
} else {
    echo "demo.demoFlag: FEATURE IS OFF\n";
}

echo '</pre>';