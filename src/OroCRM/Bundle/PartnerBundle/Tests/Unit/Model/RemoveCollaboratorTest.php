<?php

namespace OroCRM\Bundle\PartnerBundle\Tests\Unit\Model;

use OroCRM\Bundle\PartnerBundle\Model\Action\RemoveCollaborator;

class RemoveCollaboratorTest extends AbstractCollaboratorActionTestCase
{
    /**
     * @var RemoveCollaborator
     */
    protected $target;

    protected function setUp()
    {
        parent::setUp();
        $this->target = new RemoveCollaborator($this->contextAccessor, $this->manager);
    }

    public function testExecute()
    {
        $context = array();
        $usernameKey = 'key';
        $username = 'AlexSmith';
        $options = array(
            RemoveCollaborator::OPTION_KEY_USERNAME => $usernameKey
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
}
