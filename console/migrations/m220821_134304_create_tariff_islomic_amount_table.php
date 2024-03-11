<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%tariff_islomic_amount}}`.
 */
class m220821_134304_create_tariff_islomic_amount_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%tariff_islomic_amount}}', [
            'id' => $this->primaryKey(),
            'kasko_tariff_id' => $this->integer(),
            'auto_risk_type_id' => $this->integer(),
            'amount' => $this->double(),
        ]);

        $this->addForeignKey(
            'fk-tariff_islomic_amount-kasko_tariff_id',
            'tariff_islomic_amount',
            'kasko_tariff_id',
            'kasko_tariff',
            'id',
            'cascade'
        );

        $this->addForeignKey(
            'fk-tariff_islomic_amount-auto_risk_type_id',
            'tariff_islomic_amount',
            'auto_risk_type_id',
            'auto_risk_type',
            'id',
            'cascade'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%tariff_islomic_amount}}');
    }
}
