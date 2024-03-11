<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%product_promo}}`.
 */
class m230228_094558_create_product_promo_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%product_promo}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer(),
            'promo_id' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%product_promo}}');
    }
}
