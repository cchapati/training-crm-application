<?php

namespace OroCRM\Bundle\PartnerBundle\Tests\Unit\Provider;

use OroCRM\Bundle\PartnerBundle\Provider\ConfigurationProvider;

class ConfigurationProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConfigurationProvider
     */
    protected $provider;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configManager;

    protected function setUp()
    {
        $this->configManager = $this->getMockBuilder('Oro\Bundle\ConfigBundle\Config\ConfigManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->provider = new ConfigurationProvider($this->configManager);
    }

    public function testGetUser()
    {
        $expected = 'TestUsername';
        $this->configManager->expects($this->once())
            ->method('get')
            ->with(ConfigurationProvider::USERNAME_FIELD)
            ->will($this->returnValue($expected));
        $actual = $this->provider->getUsername();
        $this->assertEquals($expected, $actual);
    }

    public function testGetApiToken()
    {
        $expected = 'test_token';
        $this->configManager->expects($this->once())
            ->method('get')
            ->with(ConfigurationProvider::TOKEN_FIELD)
            ->will($this->returnValue($expected));
        $actual = $this->provider->getApiToken();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider getRepositoriesDataProvider
     */
    public function testGetRepositories($repositories, $expected)
    {
        $this->configManager->expects($this->once())
            ->method('get')
            ->with(ConfigurationProvider::REPOSITORIES_FIELD)
            ->will($this->returnValue($repositories));
        $actual = $this->provider->getRepositories();
        $this->assertEquals($expected, $actual);
    }

    public function getRepositoriesDataProvider()
    {
        $firstRepository = 'firstRepo';
        $secondRepository = 'secondRepo';
        $thirdRepository = 'thirdRepo';
        return array(
            'repositories correctly explode' => array(
                'repositories' => "{$firstRepository}\r{$secondRepository}\r\n{$thirdRepository}",
                'expected' => array(
                    $firstRepository,
                    $secondRepository,
                    $thirdRepository
                )
            ),
            'repositories correctly explode if spaces presented' => array(
                'repositories' => "  {$firstRepository}  \n  {$secondRepository} \r\n{$thirdRepository} ",
                'expected' => array(
                    $firstRepository,
                    $secondRepository,
                    $thirdRepository
                )
            )
        );
    }
}
