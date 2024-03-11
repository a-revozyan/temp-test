<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%tariff_car_accessory_coeff}}`.
 */
class m220907_180223_create_tariff_car_accessory_coeff_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%tariff_car_accessory_coeff}}', [
            'id' => $this->primaryKey(),
            'tariff_id' => $this->integer()->unsigned(),
            'car_accessory_id' => $this->integer()->unsigned(),
            'coeff' => $this->double(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%tariff_car_accessory_coeff}}');
    }
}
