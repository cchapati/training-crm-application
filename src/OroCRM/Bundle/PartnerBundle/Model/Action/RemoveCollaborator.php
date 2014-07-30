<?php

namespace OroCRM\Bundle\PartnerBundle\Model\Action;

class RemoveCollaborator extends AbstractCollaboratorAction
{
    /**
     * {@inheritdoc}
     */
    protected function executeAction($context)
    {
        $username = $this->getGitHubUsername($context);
        $this->gitHubCollaboratorManager->removeCollaborator($username);
    }
}
