<?php

namespace OroCRM\Bundle\AccountBundle\Migrations\Schema\v1_1;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

use Oro\Bundle\AttachmentBundle\Migration\Extension\AttachmentExtension;
use Oro\Bundle\AttachmentBundle\Migration\Extension\AttachmentExtensionAwareInterface;

use Oro\Bundle\NoteBundle\Migration\Extension\NoteExtension;
use Oro\Bundle\NoteBundle\Migration\Extension\NoteExtensionAwareInterface;

use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtension;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtensionAwareInterface;

class OroCRMPartnerExtensions implements
    Migration,
    AttachmentExtensionAwareInterface,
    NoteExtensionAwareInterface,
    ActivityExtensionAwareInterface
{
    /** @var AttachmentExtension */
    protected $attachmentExtension;

    /** @var NoteExtension */
    protected $noteExtension;

    /** @var ActivityExtension */
    protected $activityExtension;

    /**
     * {@inheritdoc}
     */
    public function setAttachmentExtension(AttachmentExtension $attachmentExtension)
    {
        $this->attachmentExtension = $attachmentExtension;
    }

    /**
     * {@inheritdoc}
     */
    public function setNoteExtension(NoteExtension $noteExtension)
    {
        $this->noteExtension = $noteExtension;
    }

    /**
     * {@inheritdoc}
     */
    public function setActivityExtension(ActivityExtension $activityExtension)
    {
        $this->activityExtension = $activityExtension;
    }

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->attachmentExtension->addFileRelation($schema, 'orocrm_partner', 'contract', 'file', [], 7);

        $this->attachmentExtension->addAttachmentAssociation(
            $schema,
            'orocrm_partner',
            [
                'image/*',
                'application/pdf',
                'application/zip',
                'application/x-gzip',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation'
            ],
            2
        );

        $this->noteExtension->addNoteAssociation($schema, 'orocrm_partner');

        $this->activityExtension->addActivityAssociation($schema, 'oro_email', 'orocrm_partner');
    }
}
