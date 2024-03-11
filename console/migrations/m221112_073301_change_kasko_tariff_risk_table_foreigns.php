<?php

use yii\db\Migration;

/**
 * Class m221112_073301_change_kasko_tariff_risk_table_foreigns
 */
class m221112_073301_change_kasko_tariff_risk_table_foreigns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey(
            'fk-kasko_tariff_risk-risk_id',
            'kasko_tariff_risk'
        );

        $this->addForeignKey(
            'fk-kasko_tariff_risk-risk_id',
            'kasko_tariff_risk',
            'risk_id',
            'kasko_risk',
            'id',
            'CASCADE'
        );

        $this->dropForeignKey(
            'fk-kasko_tariff_risk-tariff_id',
            'kasko_tariff_risk'
        );

        $this->addForeignKey(
            'fk-kasko_tariff_risk-tariff_id',
            'kasko_tariff_risk',
            'tariff_id',
            'kasko_tariff',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221112_073301_change_kasko_tariff_risk_table_foreigns cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221112_073301_change_kasko_tariff_risk_table_foreigns cannot be reverted.\n";

        return false;
    }
    */
}
