<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%sms_history}}`.
 */
class m221220_085938_create_sms_history_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%sms_history}}', [
            'id' => $this->primaryKey(),
            'f_user_id' => $this->integer(),
            'phone' => $this->string(),
            'telegram_chat_id' => $this->integer(),
            'message' => $this->string(),
            'to_telegram' => $this->boolean(),
            'status' => $this->integer(),
            'response_of_sms_service' => $this->string(1000),
            'sent_at' => $this->timestamp(),
            'sent_by' => $this->integer(), //user_id
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%sms_history}}');
    }
}
