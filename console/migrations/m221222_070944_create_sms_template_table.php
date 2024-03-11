<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%sms_template}}`.
 */
class m221222_070944_create_sms_template_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%sms_template}}', [
            'id' => $this->primaryKey(),
            'text' => $this->text(),
            'method' => $this->string(),
            'file_url' => $this->string(),
            //filter
            'region_car_numbers' => $this->string(), //10,30,80,
            'number_drivers_id' => $this->integer(),
            'registered_from_date' => $this->timestamp(),
            'registered_till_date' => $this->timestamp(),
            'bought_from_date' => $this->timestamp(),
            'bought_till_date' => $this->timestamp(),
            'type' => $this->integer(),
            //filter
            'all_users_count' => $this->integer(),
            'status' => $this->integer(),
            'created_at' => $this->timestamp(),
            'updated_at' => $this->timestamp(),
            'begin_date' => $this->timestamp(),
            'end_date' => $this->timestamp(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%sms_template}}');
    }
}
