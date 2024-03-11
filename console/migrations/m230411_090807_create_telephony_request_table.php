<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%telephony_requestes}}`.
 */
class m230411_090807_create_telephony_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%telephony_request}}', [
            'id' => $this->primaryKey(),
            'url' => $this->string(),
            'request' => $this->string(),
            'response' => $this->string(1000),
            'send_date' => $this->timestamp(),
            'f_user_id' => $this->integer(),
            'phone_number' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%telephony_requestes}}');
    }
}
