<?php

namespace OroCRM\Bundle\PartnerBundle\Model\Action;

use Oro\Bundle\WorkflowBundle\Exception\ActionException;
use Oro\Bundle\WorkflowBundle\Model\Action\AbstractAction;
use Oro\Bundle\WorkflowBundle\Model\ContextAccessor;

use OroCRM\Bundle\PartnerBundle\Model\GitHubCollaboratorManager;

abstract class AbstractCollaboratorAction extends AbstractAction
{
    const OPTION_KEY_USERNAME = 'username';

    /**
     * @var GitHubCollaboratorManager
     */
    protected $gitHubCollaboratorManager;

    /**
     * @var string
     */
    protected $username;

    /**
     * @param ContextAccessor $contextAccessor
     * @param GitHubCollaboratorManager $gitHubCollaboratorManager
     */
    public function __construct(ContextAccessor $contextAccessor, GitHubCollaboratorManager $gitHubCollaboratorManager)
    {
        $this->gitHubCollaboratorManager = $gitHubCollaboratorManager;
        parent::__construct($contextAccessor);
    }

    /**
     * @param mixed $context
     * @return string
     * @throws ActionException
     */
    protected function getGitHubUsername($context)
    {
        $username = $this->contextAccessor->getValue($context, $this->username);

        if (!$username) {
            throw new ActionException('GitHub username can\'t be empty.');
        }

        return $username;
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(array $options)
    {
        if (1 !== count($options)) {
            throw new ActionException(
                sprintf(
                    'Options must have 1 element, but %d given.',
                    count($options)
                )
            );
        }

        if (isset($options[self::OPTION_KEY_USERNAME])) {
            $this->username = $options[self::OPTION_KEY_USERNAME];
        } elseif (isset($options[0])) {
            $this->username = $options[0];
        } else {
            throw new ActionException(sprintf('Option "%s" is required.', self::OPTION_KEY_USERNAME));
        }
    }
}
