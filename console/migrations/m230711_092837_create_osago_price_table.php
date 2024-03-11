<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%osago_price}}`.
 */
class m230711_092837_create_osago_price_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%osago_price}}', [
            'id' => $this->primaryKey(),
            'vehicle' => $this->integer(),
            'use_territory' => $this->integer(),
            'period' => $this->integer(),
            'driver_limit' => $this->integer(),
            'discount' => $this->integer(),
            'amount' => $this->integer(),
            'updated_at' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%osago_price}}');
    }
}
