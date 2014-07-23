<?php

namespace OroCRM\Bundle\AccountBundle\Migrations\Schema\v1_0;

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
        /** Generate table orocrm_partner **/
        $table = $schema->createTable('orocrm_partner');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('account_id', 'integer', []);
        $table->addColumn('status', 'string', ['notnull' => false, 'length' => 16]);
        $table->addColumn('user_owner_id', 'integer', ['notnull' => false]);
        $table->addColumn('start_date', 'date', []);
        $table->addColumn('partner_condition', 'text', ['notnull' => false]);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['account_id'], 'UNIQ_3D7BBBC39B6B5FBA');
        $table->addIndex(['status'], 'IDX_3D7BBBC37B00651C', []);
        $table->addIndex(['user_owner_id'], 'IDX_3D7BBBC39EB185F9', []);
        /** End of generate table orocrm_partner_status **/

        /** Generate table orocrm_partner_status **/
        $table = $schema->createTable('orocrm_partner_status');
        $table->addColumn('name', 'string', ['length' => 16]);
        $table->addColumn('label', 'string', ['length' => 255]);
        $table->addColumn('`order`', 'integer', []);
        $table->setPrimaryKey(['name']);
        /** End of generate table orocrm_partner_status **/

        /** Generate table orocrm_partner_status_trans **/
        $table = $schema->createTable('orocrm_partner_status_trans');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('foreign_key', 'string', ['length' => 32]);
        $table->addColumn('content', 'string', ['length' => 255]);
        $table->addColumn('locale', 'string', ['length' => 8]);
        $table->addColumn('object_class', 'string', ['length' => 255]);
        $table->addColumn('field', 'string', ['length' => 32]);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['locale', 'object_class', 'field', 'foreign_key'], 'orocrm_partner_status_trans_idx', []);
        /** End of generate table orocrm_partner_status_trans **/

        /** Generate foreign keys for table orocrm_partner **/
        $table = $schema->getTable('orocrm_partner');
        $table->addForeignKeyConstraint(
            $schema->getTable('orocrm_account'),
            ['account_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('orocrm_partner_status'),
            ['status'],
            ['name'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_user'),
            ['user_owner_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        /** End of generate foreign keys for table orocrm_partner **/
    }
}
