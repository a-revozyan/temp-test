<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%hamkorpay_request}}`.
 */
class m221209_115304_create_hamkorpay_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%hamkorpay_request}}', [
            'id' => $this->primaryKey(),
            'url' => $this->string(),
            'request_body' => $this->text(),
            'response_body' => $this->text(),
            'send_date' => $this->dateTime(),
            'model_class' => $this->string(),
            'model_id' => $this->integer()->unsigned(),
            'token' => $this->string(1000),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%hamkorpay_request}}');
    }
}
