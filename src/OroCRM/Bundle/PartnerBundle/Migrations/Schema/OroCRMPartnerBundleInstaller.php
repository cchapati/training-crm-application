<?php

namespace Oro\Bundle\PartnerBundle\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

use OroCRM\Bundle\PartnerBundle\Migrations\Schema\v1_0\OroCRMPartnerBundle;

class OroPartnerBundleInstaller implements Installation
{
    /**
     * {@inheritdoc}
     */
    public function getMigrationVersion()
    {
        return 'v1_0';
    }

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        OroCRMPartnerBundle::createPartnerTables($schema);
    }
}
