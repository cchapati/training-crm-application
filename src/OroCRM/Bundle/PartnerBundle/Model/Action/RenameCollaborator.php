<?php

namespace OroCRM\Bundle\PartnerBundle\Model\Action;

use Symfony\Component\PropertyAccess\PropertyPath;

use Psr\Log\LoggerInterface;

use Oro\Bundle\WorkflowBundle\Model\ContextAccessor;
use OroCRM\Bundle\PartnerBundle\Exception\PartnerException;
use OroCRM\Bundle\PartnerBundle\Model\GitHubCollaboratorManager;

class RenameCollaborator extends AbstractCollaboratorAction
{
    /**
     * @var AddCollaborator
     */
    protected $addCollaborator;

    /**
     * @var RemoveCollaborator
     */
    protected $removeCollaborator;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param ContextAccessor           $contextAccessor
     * @param GitHubCollaboratorManager $gitHubCollaboratorManager
     * @param AddCollaborator           $addCollaborator
     * @param RemoveCollaborator        $removeCollaborator
     * @param LoggerInterface           $logger
     */
    public function __construct(
        ContextAccessor $contextAccessor,
        GitHubCollaboratorManager $gitHubCollaboratorManager,
        AddCollaborator $addCollaborator,
        RemoveCollaborator $removeCollaborator,
        LoggerInterface $logger
    ) {
        parent::__construct($contextAccessor, $gitHubCollaboratorManager);
        $this->addCollaborator = $addCollaborator;
        $this->removeCollaborator = $removeCollaborator;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    protected function executeAction($context)
    {
        try {
            $this->removeCollaborator->initialize(
                array(self::OPTION_KEY_USERNAME => new PropertyPath('old'))
            );
            $this->removeCollaborator->execute($context);
        } catch (PartnerException $exception) {
            $this->logger->warning($exception->getMessage(), array('exception' => $exception));
        }
        $this->addCollaborator->initialize($this->options);
        $this->addCollaborator->execute($context);
    }
}
