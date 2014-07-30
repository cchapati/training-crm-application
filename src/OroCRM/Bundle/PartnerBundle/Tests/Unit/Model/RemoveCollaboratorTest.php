<?php

namespace OroCRM\Bundle\PartnerBundle\Tests\Unit\Model;

use OroCRM\Bundle\PartnerBundle\Model\Action\AbstractCollaboratorAction;
use OroCRM\Bundle\PartnerBundle\Model\Action\RemoveCollaborator;

class RemoveCollaboratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RemoveCollaborator
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

    protected function setUp()
    {
        $this->contextAccessor = $this->getMock('Oro\Bundle\WorkflowBundle\Model\ContextAccessor');
        $this->manager = $this->getMockBuilder('OroCRM\Bundle\PartnerBundle\Model\GitHubCollaboratorManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->target = new RemoveCollaborator($this->contextAccessor, $this->manager);
    }

    /**
     * @expectedException \OroCRM\Bundle\PartnerBundle\Exception\InvalidParameterException
     * @expectedExceptionMessage GitHub account or username is required
     */
    public function testInitialiseThrowExceptionIfOptionsIncorrect()
    {
        $this->target->initialize(array());
    }

    public function testExecuteWithUsername()
    {
        $context = array();
        $usernameKey = 'key';
        $username = 'AlexSmith';
        $options = array(
            AbstractCollaboratorAction::OPTION_KEY_USERNAME => $usernameKey
        );

        $this->contextAccessor->expects($this->any())
            ->method('getValue')
            ->with($context, $usernameKey)
            ->will($this->returnValue($username));

        $this->manager->expects($this->once())
            ->method('removeCollaborator')
            ->with($username);

        $this->target->initialize($options);
        $this->target->execute($context);
    }

    /**
     * @expectedException \OroCRM\Bundle\PartnerBundle\Exception\InvalidParameterException
     * @expectedExceptionMessage Git hub username not found
     */
    public function testExecuteWithUsernameThrowExceptionIfUsernameBlank()
    {
        $context = array();
        $usernameKey = 'key';
        $username = '';
        $options = array(
            AbstractCollaboratorAction::OPTION_KEY_USERNAME => $usernameKey
        );

        $this->contextAccessor->expects($this->any())
            ->method('getValue')
            ->with($context, $usernameKey)
            ->will($this->returnValue($username));

        $this->target->initialize($options);
        $this->target->execute($context);
    }

    public function testExecuteWithAccount()
    {
        $context = array();
        $accountKey = 'key';
        $username = 'AlexSmith';
        $options = array(
            AbstractCollaboratorAction::OPTION_KEY_ACCOUNT => $accountKey
        );

        $account  =$this->getMock('OroCRM\Bundle\PartnerBundle\Entity\GitHubAccount');
        $account->expects($this->once())
            ->method('getUsername')
            ->will($this->returnValue($username));
        $this->contextAccessor->expects($this->any())
            ->method('getValue')
            ->with($context, $accountKey)
            ->will($this->returnValue($account));

        $this->manager->expects($this->once())
            ->method('removeCollaborator')
            ->with($username);

        $this->target->initialize($options);
        $this->target->execute($context);
    }

    /**
     * @expectedException \OroCRM\Bundle\PartnerBundle\Exception\InvalidParameterException
     * @expectedExceptionMessage Git hub account not found
     */
    public function testExecuteWithAccountThrowExceptionIfAccountIsNull()
    {
        $context = array();
        $accountKey = 'key';
        $options = array(
            AbstractCollaboratorAction::OPTION_KEY_ACCOUNT => $accountKey
        );

        $this->contextAccessor->expects($this->any())
            ->method('getValue')
            ->with($context, $accountKey)
            ->will($this->returnValue(null));
        $this->target->initialize($options);
        $this->target->execute($context);
    }
}
