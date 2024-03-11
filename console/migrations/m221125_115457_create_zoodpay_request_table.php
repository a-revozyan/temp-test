<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%zoodpay_request}}`.
 */
class m221125_115457_create_zoodpay_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%zoodpay_request}}', [
            'id' => $this->primaryKey(),
            'url' => $this->string(),
            'request_body' => $this->text(),
            'response_body' => $this->text(),
            'response_status_code' => $this->integer(),
            'send_date' => $this->timestamp(),
            'model_id' => $this->integer()->unsigned(),
            'model_class' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%zoodpay_request}}');
    }
}
