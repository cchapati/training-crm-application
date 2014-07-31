<?php

namespace OroCRM\Bundle\PartnerBundle\Tests\Unit\Model;

use OroCRM\Bundle\PartnerBundle\Model\Action\AbstractCollaboratorAction;

abstract class AbstractCollaboratorActionTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractCollaboratorAction
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
    }

    /**
     * @expectedException \Oro\Bundle\WorkflowBundle\Exception\ActionException
     * @expectedExceptionMessage Option "username" is required.
     */
    public function testInitialiseThrowExceptionIfOptionsIncorrect()
    {
        $this->target->initialize(array('foo' => 'bar'));
    }

    /**
     * @expectedException \Oro\Bundle\WorkflowBundle\Exception\ActionException
     * @expectedExceptionMessage Options must have 1 element, but 0 given.
     */
    public function testInitialiseThrowExceptionIfOptionsEmpty()
    {
        $this->target->initialize(array());
    }

    /**
     * @expectedException \Oro\Bundle\WorkflowBundle\Exception\ActionException
     * @expectedExceptionMessage GitHub username can't be empty.
     */
    public function testExecuteWithUsernameThrowExceptionIfUsernameBlank()
    {
        $context = array();
        $usernameKey = 'key';
        $username = '';
        $options = array(
            AbstractCollaboratorAction::OPTION_KEY_USERNAME => $usernameKey
        );

        $this->contextAccessor->expects($this->once())
            ->method('getValue')
            ->with($context, $usernameKey)
            ->will($this->returnValue($username));

        $this->target->initialize($options);
        $this->target->execute($context);
    }
}
