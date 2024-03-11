<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%accident_price}}`.
 */
class m230711_091734_create_accident_price_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%accident_price}}', [
            'id' => $this->primaryKey(),
            'gross' => $this->integer(),
            'kapital' => $this->integer(),
            'updated_at' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%accident_price}}');
    }
}
