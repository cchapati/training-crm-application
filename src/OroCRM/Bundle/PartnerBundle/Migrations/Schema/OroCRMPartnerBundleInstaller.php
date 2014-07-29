<?php

namespace OroCRM\Bundle\PartnerBundle\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtension;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtensionAwareInterface;
use Oro\Bundle\AttachmentBundle\Migration\Extension\AttachmentExtension;
use Oro\Bundle\AttachmentBundle\Migration\Extension\AttachmentExtensionAwareInterface;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

use Oro\Bundle\NoteBundle\Migration\Extension\NoteExtension;
use Oro\Bundle\NoteBundle\Migration\Extension\NoteExtensionAwareInterface;
use OroCRM\Bundle\PartnerBundle\Migrations\Schema\v1_0\OroCRMPartnerBundle;
use OroCRM\Bundle\PartnerBundle\Migrations\Schema\v1_1\OroCRMPartnerExtensions;

class OroPartnerBundleInstaller implements
    Installation,
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
    public function getMigrationVersion()
    {
        return 'v1_1';
    }

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        OroCRMPartnerBundle::createPartnerTables($schema);
        $extension = new OroCRMPartnerExtensions();
        $extension->setAttachmentExtension($this->attachmentExtension);
        $extension->setActivityExtension($this->activityExtension);
        $extension->setNoteExtension($this->noteExtension);
        $extension->up($schema, $queries);
    }
}
