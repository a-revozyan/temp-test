<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%partner_month_car_price_pay}}`.
 */
class m230828_052455_create_partner_month_car_price_pay_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%partner_month_car_price_pay}}', [
            'id' => $this->primaryKey(),
            'partner_id' => $this->integer(),
            'month' => $this->string(),
            'is_paid' => $this->boolean(),
            'updated_at' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%partner_month_car_price_pay}}');
    }
}
