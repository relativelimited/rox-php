<?php

namespace Rox\Core\Network;

class HttpErrorResponse extends AbstractHttpResponse
{
    /**
     * @var int $_statusCode
     */
    private $_statusCode;

    /**
     * @var string $_content
     */
    private $_content;

    /**
     * HttpErrorResponse constructor.
     * @param int $_statusCode
     * @param string $_content
     */
    public function __construct($_statusCode, $_content)
    {
        $this->_statusCode = $_statusCode;
        $this->_content = $_content;
    }

    /**
     * @inheritDoc
     */
    function getStatusCode()
    {
        return $this->_statusCode;
    }

    /**
     * @inheritDoc
     */
    function getContent()
    {
        return new StringResponseContent($this->_content);
    }
}
