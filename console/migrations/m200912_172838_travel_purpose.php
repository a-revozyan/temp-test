<?php

use yii\db\Migration;

/**
 * Class m200912_172838_travel_purpose
 */
class m200912_172838_travel_purpose extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropIndex(
            'idx-travel_purpose-partner_id',
            'travel_purpose'
        );

        $this->dropForeignKey(
            'fk-travel_purpose-partner_id',
            'travel_purpose'
        );

        $this->dropColumn('travel_purpose', 'partner_id');
        $this->dropColumn('travel_purpose', 'coeff');

        $this->createTable('{{%travel_partner_purpose}}', [
            'id' => $this->primaryKey(),
            'partner_id' => $this->integer()->notNull(),
            'purpose_id' => $this->integer()->notNull(),
            'coeff' => $this->float()->notNull()->defaultValue(1),
        ], null);

        $this->createIndex(
            'idx-travel_partner_purpose-partner_id',
            'travel_partner_purpose',
            'partner_id'
        );

        $this->addForeignKey(
            'fk-travel_partner_purpose-partner_id',
            'travel_partner_purpose',
            'partner_id',
            'partner',
            'id',
            'RESTRICT'
        );

        $this->createIndex(
            'idx-travel_partner_purpose-purpose_id',
            'travel_partner_purpose',
            'purpose_id'
        );

        $this->addForeignKey(
            'fk-travel_partner_purpose-purpose_id',
            'travel_partner_purpose',
            'purpose_id',
            'travel_purpose',
            'id',
            'RESTRICT'
        );

        
        $this->dropIndex(
            'idx-travel_group_type-partner_id',
            'travel_group_type'
        );

        $this->dropForeignKey(
            'fk-travel_group_type-partner_id',
            'travel_group_type'
        );

        $this->dropColumn('travel_group_type', 'partner_id');
        $this->dropColumn('travel_group_type', 'coeff');

        $this->createTable('{{%travel_partner_group_type}}', [
            'id' => $this->primaryKey(),
            'partner_id' => $this->integer()->notNull(),
            'group_type_id' => $this->integer()->notNull(),
            'coeff' => $this->float()->notNull()->defaultValue(1),
        ], null);

        $this->createIndex(
            'idx-travel_partner_group_type-partner_id',
            'travel_partner_group_type',
            'partner_id'
        );

        $this->addForeignKey(
            'fk-travel_partner_group_type-partner_id',
            'travel_partner_group_type',
            'partner_id',
            'partner',
            'id',
            'RESTRICT'
        );

        $this->createIndex(
            'idx-travel_partner_group_type-purpose_id',
            'travel_partner_group_type',
            'group_type_id'
        );

        $this->addForeignKey(
            'fk-travel_partner_group_type-purpose_id',
            'travel_partner_group_type',
            'group_type_id',
            'travel_group_type',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200912_172838_travel_purpose cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200912_172838_travel_purpose cannot be reverted.\n";

        return false;
    }
    */
}
