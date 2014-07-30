<?php

namespace OroCRM\Bundle\PartnerBundle\Tests\Unit\Model;

use OroCRM\Bundle\PartnerBundle\Model\Action\AbstractCollaboratorAction;
use OroCRM\Bundle\PartnerBundle\Model\Action\RenameCollaborator;

class RenameCollaboratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RenameCollaborator
     */
    protected $target;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextAccessor;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $manager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $addCollaborator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $removeCollaborator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $logger;

    protected function setUp()
    {
        $this->contextAccessor = $this->getMock('Oro\Bundle\WorkflowBundle\Model\ContextAccessor');
        $this->manager = $this->getMockBuilder('OroCRM\Bundle\PartnerBundle\Model\GitHubCollaboratorManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->addCollaborator = $this->getMockBuilder('OroCRM\Bundle\PartnerBundle\Model\Action\AddCollaborator')
            ->disableOriginalConstructor()
            ->getMock();
        $this->removeCollaborator = $this->getMockBuilder('OroCRM\Bundle\PartnerBundle\Model\Action\RemoveCollaborator')
            ->disableOriginalConstructor()
            ->getMock();
        $this->logger = $this->getMock('Psr\Log\LoggerInterface');
        $this->target = new RenameCollaborator(
            $this->contextAccessor,
            $this->manager,
            $this->addCollaborator,
            $this->removeCollaborator,
            $this->logger
        );
    }

    /**
     * @expectedException \OroCRM\Bundle\PartnerBundle\Exception\InvalidParameterException
     * @expectedExceptionMessage GitHub account or username is required
     */
    public function testInitialiseThrowExceptionIfOptionsIncorrect()
    {
        $this->target->initialize(array());
    }

    public function testExecute()
    {
        $usernameKey = 'username';
        $context = array($usernameKey => 'AlexSmith');
        $this->removeCollaborator->expects($this->once())
            ->method('initialize')
            ->with(
                $this->callback(
                    function ($options) {
                        $this->assertArrayHasKey(AbstractCollaboratorAction::OPTION_KEY_USERNAME, $options);
                        $this->assertEquals('old', (string)$options[AbstractCollaboratorAction::OPTION_KEY_USERNAME]);
                        return true;
                    }
                )
            );
        $exceptionMessage = 'Not found';

        $exception = $this->getMockBuilder('OroCRM\Bundle\PartnerBundle\Exception\InvalidResponseException')
            ->setConstructorArgs(array($exceptionMessage))
            ->getMock();

        $this->logger->expects($this->once())
            ->method('warning')
            ->with($exceptionMessage, array('exception' => $exception));
        $this->removeCollaborator->expects($this->once())
            ->method('execute')
            ->with($context)
            ->will($this->throwException($exception));
        $options = array(AbstractCollaboratorAction::OPTION_KEY_USERNAME => $usernameKey);
        $this->addCollaborator->expects($this->once())
            ->method('initialize')
            ->with($options);
        $this->addCollaborator->expects($this->once())
            ->method('execute')
            ->with($context);
        $this->target->initialize($options);
        $this->target->execute($context);
    }
}
