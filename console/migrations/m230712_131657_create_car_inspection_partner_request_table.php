<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%car_inspection_partner_request}}`.
 */
class m230712_131657_create_car_inspection_partner_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%car_inspection_partner_request}}', [
            'id' => $this->primaryKey(),
            'url' => $this->string(),
            'request_body' => $this->string(),
            'response_body' => $this->text(),
            'partner_id' => $this->integer(),
            'taken_time' => $this->integer(),
            'send_date' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%car_inspection_partner_request}}');
    }
}
