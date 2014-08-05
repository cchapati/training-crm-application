<?php

namespace OroCRM\Bundle\PartnerBundle\Tests\Unit\Model;

use Github\Client;

use OroCRM\Bundle\PartnerBundle\Model\GitHubCollaboratorManager;

class GitHubCollaboratorManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GitHubCollaboratorManager
     */
    protected $target;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configuration;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $clientFactory;

    protected function setUp()
    {
        $this->configuration = $this->getMockBuilder('OroCRM\Bundle\PartnerBundle\Provider\ConfigurationProvider')
            ->disableOriginalConstructor()
            ->getMock();

        $this->clientFactory = $this->getMock('OroCRM\Bundle\PartnerBundle\Model\GitHubClientFactory');

        $this->target = new GitHubCollaboratorManager($this->configuration, $this->clientFactory);
    }

    /**
     * @expectedException \OroCRM\Bundle\PartnerBundle\Exception\InvalidConfigurationException
     * @expectedExceptionMessage GitHub API token isn't set.
     */
    public function testAddCollaboratorThrowExceptionIfTokenNotSet()
    {
        $this->configuration->expects($this->once())
            ->method('getTeams')
            ->will(
                $this->returnValue(
                    array(
                        array('team')
                    )
                )
            );
        $this->target->addCollaborator('test');
    }

    public function testClientCreatesOnlyOnceAndAuthenticate()
    {
        $this->getClient(array(array('name' => 'testTeam', 'id' => 1)), 4);
        $this->target->addCollaborator('test');
        $this->target->addCollaborator('test');
        $this->target->removeCollaborator('test');
        $this->target->removeCollaborator('test');
    }

    /**
     * @dataProvider addRemoveDataProvider
     */
    public function testAddCollaborator(array $data, array $expected)
    {
        $expectedTeams = $expected['teams'];
        $teamsAPI = $this->getClient($data['teams']);
        for ($i = 0; $i < count($expectedTeams); $i++) {
            $teamsAPI->expects($this->at($i+1))
                ->method('addMember')
                ->with(
                    $expectedTeams[$i]['id'],
                    $expected['username']
                );
        }
        $this->target->addCollaborator($data['username']);
    }

    public function testAddCollaboratorThrowAnExceptionIfRequestToApiFailed()
    {
        $username = 'James';
        $team = array('name' => 'testTeam', 'id' => 1);
        $reason = 'Not Found';
        $teamsAPI = $this->getClient(array($team));
        $exception = $this->getMockBuilder('Github\Exception\RuntimeException')
            ->setConstructorArgs(array($reason))
            ->getMock();
        $teamsAPI->expects($this->once())
            ->method('addMember')
            ->with($team['id'], $username)
            ->will($this->throwException($exception));
        $this->setExpectedException(
            'OroCRM\Bundle\PartnerBundle\Exception\InvalidResponseException',
            'Can\'t add user "James" to GitHub team "testTeam". Reason: ' . $reason,
            0
        );
        $this->target->addCollaborator($username);
    }

    /**
     * @dataProvider addRemoveDataProvider
     */
    public function testRemoveCollaborator(array $data, array $expected)
    {
        $teamsAPI = $this->getClient($data['teams']);
        $expectedTeams = $expected['teams'];
        for ($i = 0; $i < count($expectedTeams); $i++) {
            $teamsAPI->expects($this->at($i+1))
                ->method('removeMember')
                ->with(
                    $expectedTeams[$i]['id'],
                    $expected['username']
                );
        }
        $this->target->removeCollaborator($data['username']);
    }

    public function testRemoveCollaboratorThrowAnExceptionIfRequestToApiFailed()
    {
        $username = 'James';
        $team = array('name' => 'testTeam', 'id' => 1);
        $reason = 'Not Found';
        $collaboratorsApi = $this->getClient(array($team));
        $exception = $this->getMockBuilder('Github\Exception\RuntimeException')
            ->setConstructorArgs(array($reason))
            ->getMock();
        $collaboratorsApi->expects($this->once())
            ->method('removeMember')
            ->with($team['id'], $username)
            ->will($this->throwException($exception));
        $this->setExpectedException(
            'OroCRM\Bundle\PartnerBundle\Exception\InvalidResponseException',
            'Can\'t remove user "James" from GitHub team "testTeam". Reason: '
            . $reason,
            0
        );
        $this->target->removeCollaborator($username);
    }

    /**
     * @param array  $teams
     * @param int    $callsCount
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getClient(array $teams, $callsCount = 1)
    {
        $organization = 'testOrganization';
        $expectedToken = '9ad4c08c-5433-4b53-91cc-b395cca21cce';
        $this->configuration->expects($this->once())
            ->method('getApiToken')
            ->will($this->returnValue($expectedToken));
        $this->configuration->expects($this->any())
            ->method('getOrganization')
            ->will($this->returnValue($organization));
        $this->configuration->expects($this->exactly($callsCount))
            ->method('getTeams')
            ->will(
                $this->returnValue(
                    array_map(
                        function ($team) {
                            return $team['name'];
                        },
                        $teams
                    )
                )
            );
        $client = $this->getMock('Github\Client');
        $this->clientFactory->expects($this->once())
            ->method('createClient')
            ->will($this->returnValue($client));
        $client->expects($this->once())
            ->method('authenticate')
            ->with($expectedToken, null, Client::AUTH_URL_TOKEN);
        $teamsAPI = $this->getMockBuilder('Github\Api\Organization\Teams')
            ->disableOriginalConstructor()
            ->getMock();
        $teamsAPI->expects($this->once())
            ->method('all')
            ->with($organization)
            ->will($this->returnValue($teams));
        $client->expects($this->atLeastOnce())
            ->method('api')
            ->with('teams')
            ->will($this->returnValue($teamsAPI));
        return $teamsAPI;
    }

    public function addRemoveDataProvider()
    {
        return array(
            'add/remove Member from team members' => array(
                'data' => array(
                    'username'     => $username = 'Alex',
                    'teams' => $repositories = array(
                        array('name' => 'TestTeam', 'id' => 42),
                        array('name' => 'DevelopersTeam', 'id' => 21),
                    )
                ),
                'expected' => array(
                    'username'     => $username,
                    'teams' => $repositories
                )
            )
        );
    }
}
