<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%auto_risk_kasko_tariff}}`.
 */
class m220913_184951_create_auto_risk_kasko_tariff_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%auto_risk_kasko_tariff}}', [
            'id' => $this->primaryKey(),
            'auto_risk_type_id' => $this->integer()->unsigned(),
            'kasko_tariff_id' => $this->integer()->unsigned(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%auto_risk_kasko_tariff}}');
    }
}
