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
            ->method('getRepositories')
            ->will(
                $this->returnValue(
                    array(
                        array('owner' => 'AlexSmith', 'name' => 'AlexSampleProject')
                    )
                )
            );
        $this->target->addCollaborator('test');
    }

    public function testClientCreatesOnlyOnceAndAuthenticate()
    {
        $this->getClient(array(array('owner' => 'AlexSmith', 'name' => 'AlexSampleProject')), 4);
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
        $collaboratorsApi = $this->getClient($data['repositories']);
        $expectedRepositories = $expected['repositories'];
        for ($i = 0; $i < count($expectedRepositories); $i++) {
            $collaboratorsApi->expects($this->at($i))
                ->method('add')
                ->with(
                    $expectedRepositories[$i]['owner'],
                    $expectedRepositories[$i]['name'],
                    $expected['username']
                );
        }
        $this->target->addCollaborator($data['username']);
    }

    public function testAddCollaboratorThrowAnExceptionIfRequestToApiFailed()
    {
        $username = 'James';
        $owner = 'AlexSmith';
        $name = 'AlexSampleProject';
        $reason = 'Not Found';
        $collaboratorsApi = $this->getClient(array(array('owner' => $owner, 'name' => $name)));
        $exception = $this->getMockBuilder('Github\Exception\RuntimeException')
            ->setConstructorArgs(array($reason))
            ->getMock();
        $collaboratorsApi->expects($this->once())
            ->method('add')
            ->with($owner, $name, $username)
            ->will($this->throwException($exception));
        $this->setExpectedException(
            'OroCRM\Bundle\PartnerBundle\Exception\InvalidResponseException',
            'Can\'t add collaborator "James" to GitHub repository "AlexSmith/AlexSampleProject". Reason: ' . $reason,
            0
        );
        $this->target->addCollaborator($username);
    }

    /**
     * @dataProvider addRemoveDataProvider
     */
    public function testRemoveCollaborator(array $data, array $expected)
    {
        $collaboratorsApi = $this->getClient($data['repositories']);
        $expectedRepositories = $expected['repositories'];
        for ($i = 0; $i < count($expectedRepositories); $i++) {
            $collaboratorsApi->expects($this->at($i))
                ->method('remove')
                ->with(
                    $expectedRepositories[$i]['owner'],
                    $expectedRepositories[$i]['name'],
                    $expected['username']
                );
        }
        $this->target->removeCollaborator($data['username']);
    }

    public function testRemoveCollaboratorThrowAnExceptionIfRequestToApiFailed()
    {
        $username = 'James';
        $owner = 'AlexSmith';
        $name = 'AlexSampleProject';
        $reason = 'Not Found';
        $collaboratorsApi = $this->getClient(array(array('owner' => $owner, 'name' => $name)));
        $exception = $this->getMockBuilder('Github\Exception\RuntimeException')
            ->setConstructorArgs(array($reason))
            ->getMock();
        $collaboratorsApi->expects($this->once())
            ->method('remove')
            ->with($owner, $name, $username)
            ->will($this->throwException($exception));
        $this->setExpectedException(
            'OroCRM\Bundle\PartnerBundle\Exception\InvalidResponseException',
            'Can\'t remove collaborator "James" from GitHub repository "AlexSmith/AlexSampleProject". Reason: '
            . $reason,
            0
        );
        $this->target->removeCollaborator($username);
    }

    /**
     * @param array $repositories
     * @param int   $callsCount
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getClient(array $repositories, $callsCount = 1)
    {
        $expectedToken = '9ad4c08c-5433-4b53-91cc-b395cca21cce';
        $this->configuration->expects($this->once())
            ->method('getApiToken')
            ->will($this->returnValue($expectedToken));
        $this->configuration->expects($this->exactly($callsCount))
            ->method('getRepositories')
            ->will($this->returnValue($repositories));
        $client = $this->getMock('Github\Client');
        $this->clientFactory->expects($this->once())
            ->method('createClient')
            ->will($this->returnValue($client));
        $client->expects($this->once())
            ->method('authenticate')
            ->with($expectedToken, null, Client::AUTH_URL_TOKEN);
        $collaboratorsApi = $this->getMockBuilder('\Github\Api\Repository\Collaborators')
            ->disableOriginalConstructor()
            ->getMock();
        $repositoryApi = $this->getMockBuilder('Github\Api\Repo')
            ->disableOriginalConstructor()
            ->getMock();
        $repositoryApi->expects($this->atLeastOnce())
            ->method('collaborators')
            ->will($this->returnValue($collaboratorsApi));
        $client->expects($this->atLeastOnce())
            ->method('api')
            ->with('repo')
            ->will($this->returnValue($repositoryApi));
        return $collaboratorsApi;
    }

    public function addRemoveDataProvider()
    {
        return array(
            'add/remove Collaborator from repository collaborators' => array(
                'data' => array(
                    'username'     => $username = 'Alex',
                    'repositories' => $repositories = array(
                        array('owner' => 'AlexSmith', 'name' => 'AlexSampleProject'),
                        array('owner' => 'AllenSmith', 'name' => 'AllenSampleProject'),
                    )
                ),
                'expected' => array(
                    'username'     => $username,
                    'repositories' => $repositories
                )
            ),
            'add/remove Collaborator ignored incorrect repositories' => array(
                'data' => array(
                    'username'     => $username = 'Alex',
                    'repositories' => $repositories = array(
                            array('owner' => 'AlexSmith', 'name' => ''),
                            array('owner' => '', 'name' => ''),
                            array('owner' => 'AlexSmith', 'name' => 'AlexSampleProject'),
                            array('owner' => 'AllenSmith', 'name' => 'AllenSampleProject'),
                        )
                ),
                'expected' => array(
                    'username'     => $username,
                    'repositories' => array(
                        array('owner' => 'AlexSmith', 'name' => 'AlexSampleProject'),
                        array('owner' => 'AllenSmith', 'name' => 'AllenSampleProject'),
                    )
                )
            )
        );
    }
}
