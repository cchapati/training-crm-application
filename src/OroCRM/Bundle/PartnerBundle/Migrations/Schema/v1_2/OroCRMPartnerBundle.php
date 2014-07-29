<?php

namespace OroCRM\Bundle\PartnerBundle\Migrations\Schema\v1_2;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class OroCRMPartnerBundle implements Migration
{
    /**
     * @inheritdoc
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        self::createGitHubAccountTable($schema);
    }

    public static function createGitHubAccountTable(Schema $schema)
    {
        /** Generate table orocrm_partner_git_hub **/
        $table = $schema->createTable('orocrm_partner_git_hub_account');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('partner_id', 'integer', ['notnull' => false]);
        $table->addColumn('created_at', 'datetime', ['notnull' => false]);
        $table->addColumn('username', 'string', ['length' => 100]);
        $table->addColumn('name', 'string', ['notnull' => false, 'length' => 100]);
        $table->addColumn('email', 'string', ['notnull' => false, 'length' => 100]);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['partner_id'], 'IDX_4ECD86E59393F8FE', []);
        /** End of generate table orocrm_partner_git_hub **/

        /** Generate foreign keys for table orocrm_partner_git_hub **/
        $table->addForeignKeyConstraint(
            $schema->getTable('orocrm_partner'),
            ['partner_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        /** End of generate foreign keys for table orocrm_partner_git_hub **/
    }
}
