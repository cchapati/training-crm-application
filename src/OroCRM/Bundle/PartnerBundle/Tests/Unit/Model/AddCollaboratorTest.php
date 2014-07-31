<?php

namespace OroCRM\Bundle\PartnerBundle\Tests\Unit\Model;

use OroCRM\Bundle\PartnerBundle\Model\Action\AbstractCollaboratorAction;
use OroCRM\Bundle\PartnerBundle\Model\Action\AddCollaborator;

class AddCollaboratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AddCollaborator
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
        $this->target = new AddCollaborator($this->contextAccessor, $this->manager);
    }

    /**
     * @expectedException \OroCRM\Bundle\PartnerBundle\Exception\InvalidParameterException
     * @expectedExceptionMessage GitHub username is required
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
            ->method('addCollaborator')
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
}
