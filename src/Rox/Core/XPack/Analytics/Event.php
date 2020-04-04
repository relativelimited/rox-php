<?php

namespace Rox\Core\XPack\Analytics\Model;

use JsonSerializable;
use Rox\Core\Utils\TimeUtils;

class Event implements JsonSerializable
{
    /**
     * @var string $_flag
     */
    private $_flag;

    /**
     * @var string $_value
     */
    private $_value;

    /**
     * @var string $_distinctId
     */
    private $_distinctId;

    /**
     * @var string $_experimentId
     */
    private $_experimentId;

    /**
     * @var string $_experimentVersion
     */
    private $_experimentVersion;

    /**
     * @var string $_type
     */
    private $_type;

    /**
     * @var float $_time
     */
    private $_time;

    public function __construct()
    {
        $this->_experimentVersion = '0';
        $this->_type = 'IMPRESSION';
        $time = TimeUtils::currentTimeMillis();
        $ms = isset($_ENV['rox.analytics.ms']) ? $_ENV['rox.analytics.ms'] : null;
        if ($ms) {
            $time = floatval($ms);
        }
        $this->_time = $time;
    }

    /**
     * @return string
     */
    public function getFlag()
    {
        return $this->_flag;
    }

    /**
     * @param string $flag
     * @return Event
     */
    public function setFlag($flag)
    {
        $this->_flag = $flag;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * @param string $value
     * @return Event
     */
    public function setValue($value)
    {
        $this->_value = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getDistinctId()
    {
        return $this->_distinctId;
    }

    /**
     * @param string $distinctId
     * @return Event
     */
    public function setDistinctId($distinctId)
    {
        $this->_distinctId = $distinctId;
        return $this;
    }

    /**
     * @return string
     */
    public function getExperimentId()
    {
        return $this->_experimentId;
    }

    /**
     * @param string $experimentId
     * @return Event
     */
    public function setExperimentId($experimentId)
    {
        $this->_experimentId = $experimentId;
        return $this;
    }

    /**
     * @return string
     */
    public function getExperimentVersion()
    {
        return $this->_experimentVersion;
    }

    /**
     * @param string $experimentVersion
     * @return Event
     */
    public function setExperimentVersion($experimentVersion)
    {
        $this->_experimentVersion = $experimentVersion;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * @param string $type
     * @return Event
     */
    public function setType($type)
    {
        $this->_type = $type;
        return $this;
    }

    /**
     * @return float
     */
    public function getTime()
    {
        return $this->_time;
    }

    /**
     * @param float $time
     * @return Event
     */
    public function setTime($time)
    {
        $this->_time = $time;
        return $this;
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return json_encode([
            'flag' => $this->_flag,
            'value' => $this->_value,
            'distinctId' => $this->_distinctId,
            'experimentId' => $this->_experimentId,
            'experimentVersion' => $this->_experimentVersion,
            'type' => $this->_type,
            'time' => $this->_time
        ]);
    }
}
