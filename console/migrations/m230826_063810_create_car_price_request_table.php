<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%car_price_request}}`.
 */
class m230826_063810_create_car_price_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%car_price_request}}', [
            'id' => $this->primaryKey(),
            'brand_id' => $this->integer(),
            'model_id' => $this->integer(),
            'transmission_type' => $this->integer(),
            'fuel_type' => $this->integer(),
            'year' => $this->integer(),
            'mileage' => $this->integer(),
            'engine_capacity' => $this->float(),
            'average_price' => $this->integer(),
            'among_cars_count' => $this->integer(),
            'partner_id' => $this->integer(),
            'created_at' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%car_price_request}}');
    }
}
