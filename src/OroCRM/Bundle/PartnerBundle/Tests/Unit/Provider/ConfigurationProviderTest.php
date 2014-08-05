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
     * @dataProvider getTeamsDataProvider
     */
    public function testGetTeams($teams, $expected)
    {
        $this->configManager->expects($this->once())
            ->method('get')
            ->with(ConfigurationProvider::TEAMS_FIELD)
            ->will($this->returnValue($teams));
        $actual = $this->provider->getTeams();
        $this->assertEquals($expected, $actual);
    }

    public function getTeamsDataProvider()
    {
        $team = 'testTeam';
        $developersTeam = 'developersTeam';
        return array(
            'teams correctly explode' => array(
                'teams' => "{$team}\r{$developersTeam}",
                'expected' => array(
                    $team,
                    $developersTeam
                )
            ),
            'teams correctly explode if divided by \n' => array(
                'teams' => "  {$team}  \n  {$developersTeam} ",
                'expected' => array(
                    $team,
                    $developersTeam
                )
            ),

            'teams correctly explode if spaces presented' => array(
                'repositories' => "  {$team}  \r\n  {$developersTeam} ",
                'expected' => array(
                    $team,
                    $developersTeam
                )
            )
        );
    }
}
