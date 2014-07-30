<?php

namespace OroCRM\Bundle\PartnerBundle\Model\Action;



class AddCollaborator extends AbstractCollaboratorAction
{
    /**
     * {@inheritdoc}
     */
    protected function executeAction($context)
    {
        $username = $this->getGitHubUsername($context);
        $this->gitHubCollaboratorManager->addCollaborator($username);
    }
}
