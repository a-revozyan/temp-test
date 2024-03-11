<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%partner_auto_model}}`.
 */
class m230428_104956_create_partner_auto_model_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%partner_auto_model}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'partner_auto_brand_id' => $this->integer(),
            'created_at' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%partner_auto_model}}');
    }
}
