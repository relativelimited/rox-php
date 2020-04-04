<?php

namespace Rox\Core;

use InvalidArgumentException;
use Rox\Core\Client\DeviceProperties;
use Rox\Core\Client\DevicePropertiesInterface;
use Rox\Core\Client\RoxOptionsInterface;
use Rox\Core\Client\SdkSettingsInterface;
use Rox\RoxTestCase;

class CoreTests extends RoxTestCase
{
    /**
     * @var SdkSettingsInterface $_mockedSdkSettings
     */
    private $_mockedSdkSettings;

    /**
     * @var RoxOptionsInterface $_mockedOptions
     */
    private $_mockedOptions;

    protected function setUp()
    {
        parent::setUp();

        $this->_mockedSdkSettings = \Mockery::mock(SdkSettingsInterface::class)
            ->shouldReceive('getApiKey')
            ->andReturn('aaaaaaaaaaaaaaaaaaaaaaaa')
            ->byDefault()
            ->getMock();

        $this->_mockedOptions = \Mockery::mock(RoxOptionsInterface::class)
            ->shouldReceive('getRoxyURL')
            ->andReturn('http://site.com')
            ->byDefault()
            ->getMock()
            ->shouldReceive('getConfigurationFetchedHandler')
            ->andReturnNull()
            ->byDefault()
            ->getMock()
            ->shouldReceive('getImpressionHandler')
            ->andReturnNull()
            ->byDefault()
            ->getMock()
            ->shouldReceive('getDynamicPropertiesRule')
            ->andReturnNull()
            ->byDefault()
            ->getMock()
            ->shouldReceive('getConfigFetchIntervalInSeconds')
            ->andReturnNull()
            ->byDefault()
            ->getMock()
            ->shouldReceive('getCacheStorage')
            ->andReturnNull()
            ->byDefault()
            ->getMock()
            ->shouldReceive('isLogCacheHitsAndMisses')
            ->andReturn(false)
            ->byDefault()
            ->getMock()
            ->shouldReceive('getVersion')
            ->andReturnNull()
            ->byDefault()
            ->getMock()
            ->shouldReceive('getDevModeKey')
            ->andReturnNull()
            ->byDefault()
            ->getMock()
            ->shouldReceive('getDistinctId')
            ->andReturnNull()
            ->byDefault()
            ->getMock();
    }

    public function testWillCheckNullApiKey()
    {
        $this->setExpectedException(InvalidArgumentException::class);

        $this->_mockedSdkSettings
            ->shouldReceive('getApiKey')
            ->andReturn(null)
            ->getMock();

        $mockedDeviceProps = \Mockery::mock(DevicePropertiesInterface::class)
            ->shouldReceive('getLibVersion')
            ->andReturn("1.0.0")
            ->getMock();

        $this->_mockedOptions
            ->shouldReceive('getRoxyURL')
            ->andReturnNull()
            ->getMock();

        $c = new Core();
        $c->setup($this->_mockedSdkSettings, $mockedDeviceProps, $this->_mockedOptions);
    }

    public function testWillCheckEmptyApiKey()
    {
        $this->setExpectedException(InvalidArgumentException::class);

        $this->_mockedSdkSettings
            ->shouldReceive('getApiKey')
            ->andReturn('')
            ->getMock();

        $mockedDeviceProps = \Mockery::mock(DevicePropertiesInterface::class)
            ->shouldReceive('getLibVersion')
            ->andReturn("1.0.0")
            ->getMock();

        $this->_mockedOptions
            ->shouldReceive('getRoxyURL')
            ->andReturnNull()
            ->getMock();

        $c = new Core();
        $c->setup($this->_mockedSdkSettings, $mockedDeviceProps, $this->_mockedOptions);
    }

    public function testWillCheckInvalidApiKey()
    {
        $this->setExpectedException(InvalidArgumentException::class);

        $this->_mockedSdkSettings
            ->shouldReceive('getApiKey')
            ->andReturn('aaaaaaaaaaaaaaaaaaaaaaag')
            ->getMock();

        $mockedDeviceProps = \Mockery::mock(DevicePropertiesInterface::class)
            ->shouldReceive('getLibVersion')
            ->andReturn("1.0.0")
            ->getMock();

        $this->_mockedOptions
            ->shouldReceive('getRoxyURL')
            ->andReturnNull()
            ->getMock();

        $c = new Core();
        $c->setup($this->_mockedSdkSettings, $mockedDeviceProps, $this->_mockedOptions);
    }

    public function testWillCheckCoreSetupWhenOptionsWithRoxy()
    {
        $this->_mockedSdkSettings
            ->shouldReceive('getApiKey')
            ->andReturn('doesn\'t matter')
            ->getMock();

        $mockedDeviceProps = \Mockery::mock(DevicePropertiesInterface::class)
            ->shouldReceive('getLibVersion')
            ->andReturn("1.0.0")
            ->getMock();

        $c = new Core();
        $c->setup($this->_mockedSdkSettings, $mockedDeviceProps, $this->_mockedOptions);
    }

    public function testWillCheckCoreSetupWhenNoOptions()
    {
        $dp = new DeviceProperties($this->_mockedSdkSettings, $this->_mockedOptions);
        $c = new Core();
        $c->setup($this->_mockedSdkSettings, $dp, null);
    }
}
