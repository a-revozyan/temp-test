<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%partner_account}}`.
 */
class m230619_134503_create_partner_account_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%partner_account}}', [
            'id' => $this->primaryKey(),
            'partner_id' => $this->integer(),
            'amount' => $this->integer(),
            'note' => $this->string(500),
            'user_id' => $this->integer(),
            'created_at' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%partner_account}}');
    }
}
