<?php

namespace OroCRM\Bundle\PartnerBundle\Tests\Unit\Model;

use OroCRM\Bundle\PartnerBundle\Model\Action\AddCollaborator;

class AddCollaboratorTest extends AbstractCollaboratorActionTestCase
{
    /**
     * @var AddCollaborator
     */
    protected $target;

    protected function setUp()
    {
        parent::setUp();
        $this->target = new AddCollaborator($this->contextAccessor, $this->manager);
    }

    public function testExecute()
    {
        $context = array();
        $usernameKey = 'key';
        $username = 'AlexSmith';
        $options = array(
            AddCollaborator::OPTION_KEY_USERNAME => $usernameKey
        );

        $this->contextAccessor->expects($this->once())
            ->method('getValue')
            ->with($context, $usernameKey)
            ->will($this->returnValue($username));

        $this->manager->expects($this->once())
            ->method('addCollaborator')
            ->with($username);

        $this->target->initialize($options);
        $this->target->execute($context);
    }
}
