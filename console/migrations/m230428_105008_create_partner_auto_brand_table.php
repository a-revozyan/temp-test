<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%partner_auto_brand}}`.
 */
class m230428_105008_create_partner_auto_brand_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%partner_auto_brand}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'created_at' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%partner_auto_brand}}');
    }
}
