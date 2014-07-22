<?php

namespace OroCRM\Bundle\AccountBundle\Migrations\Schema\v1_0;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class OroCRMPartnerBundle implements Migration
{
    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        // @codingStandardsIgnoreStart

        /** Generate table orocrm_partner_status **/
        $table = $schema->createTable('orocrm_partner_status');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('status', 'string', array('length' => 32));
        $table->addColumn('`order`', 'integer');
        $table->setPrimaryKey(['id']);
        /** End of generate table orocrm_partner_status **/

        /** Generate table orocrm_partner **/
        $table = $schema->createTable('orocrm_partner');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('start_date', 'date', []);
        $table->addColumn('partner_condition', 'text', ['notnull' => false]);
        $table->addColumn('status_id', 'integer', ['notnull' => false]);
        $table->addColumn('account_id', 'integer', []);
        $table->addColumn('user_owner_id', 'integer', ['notnull' => false]);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['account_id'], 'IDX_FK_PARTNER_ACCOUNT', []);
        $table->addIndex(['user_owner_id'], 'IDX_FK_PARTNER_USER', []);
        $table->addIndex(['status_id'], 'IDX_FK_PARTNER_STATUS', []);
        $table->addForeignKeyConstraint($schema->getTable('orocrm_account'), ['account_id'], ['id'], ['onDelete' => 'CASCADE', 'onUpdate' => null]);
        $table->addForeignKeyConstraint($schema->getTable('oro_user'), ['user_owner_id'], ['id'], ['onDelete' => 'SET NULL', 'onUpdate' => null]);
        $table->addForeignKeyConstraint($schema->getTable('orocrm_partner_status'), ['status_id'], ['id'], ['onDelete' => 'SET NULL', 'onUpdate' => null]);
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
        $table->addIndex(
            ['locale', 'object_class', 'field', 'foreign_key'],
            'orocrm_partner_status_translation_idx',
            []
        );
        /** End of generate table orocrm_partner_status_trans **/

        // @codingStandardsIgnoreEnd
    }
}
