<?php

use yii\db\Migration;

/**
 * Class m200927_070518_travelextra
 */
class m200927_070518_travelextra extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropIndex(
            'idx-travel_extra_insurance-partner_id',
            'travel_extra_insurance'
        );

        $this->dropForeignKey(
            'fk-travel_extra_insurance-partner_id',
            'travel_extra_insurance'
        );

        $this->dropColumn('travel_extra_insurance', 'partner_id');
        $this->dropColumn('travel_extra_insurance', 'coeff');

        $this->createTable('{{%travel_partner_extra_insurance}}', [
            'id' => $this->primaryKey(),
            'partner_id' => $this->integer()->notNull(),
            'extra_insurance_id' => $this->integer()->notNull(),
            'coeff' => $this->float()->notNull()->defaultValue(1),
        ], null);

        $this->createIndex(
            'idx-travel_partner_extra_insurance-partner_id',
            'travel_partner_extra_insurance',
            'partner_id'
        );

        $this->addForeignKey(
            'fk-travel_partner_extra_insurance-partner_id',
            'travel_partner_extra_insurance',
            'partner_id',
            'partner',
            'id',
            'RESTRICT'
        );

        $this->createIndex(
            'idx-travel_partner_extra_insurance-extra_insurance_id',
            'travel_partner_extra_insurance',
            'extra_insurance_id'
        );

        $this->addForeignKey(
            'fk-travel_partner_extra_insurance-extra_insurance_id',
            'travel_partner_extra_insurance',
            'extra_insurance_id',
            'travel_extra_insurance',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200927_070518_travelextra cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200927_070518_travelextra cannot be reverted.\n";

        return false;
    }
    */
}
