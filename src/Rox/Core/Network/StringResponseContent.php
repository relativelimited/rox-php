<?php

namespace Rox\Core\Network;

class StringResponseContent implements HttpResponseContentInterface
{
    /**
     * @var string $_content
     */
    private $_content;

    /**
     * TestResponseContent constructor.
     * @param string $content
     */
    public function __construct($content)
    {
        $this->_content = $content;
    }

    /**
     * @return string
     */
    function readAsString()
    {
        return $this->_content;
    }
}