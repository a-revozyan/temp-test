<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%car_inspection}}`.
 */
class m230602_103908_create_car_inspection_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%car_inspection}}', [
            'id' => $this->primaryKey(),
            'uuid' => $this->string(),
            'client_id' => $this->integer(),
            'autonumber' => $this->string(),
            'vin' => $this->string(),
            'partner_auto_model_id' => $this->integer(),
            'partner_id' => $this->integer(),
            'task_id' => $this->integer(),
            'status' => $this->integer(),
            'created_at' => $this->dateTime(),
            'send_invite_sms_date' => $this->dateTime(),
            'send_verification_sms_date' => $this->dateTime(),
            'verification_code' => $this->integer(),
            'sent_sms_count' => $this->integer(),
            'big_time_of_sending_sms' => $this->dateTime(),
            'push_token' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%car_inspection}}');
    }
}
