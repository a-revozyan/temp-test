<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%neo_request}}`.
 */
class m230906_124826_create_neo_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%neo_request}}', [
            'id' => $this->primaryKey(),
            'url' => $this->string(),
            'request_body' => $this->text(),
            'response_body' => $this->text(),
            'send_date' => $this->dateTime(),
            'osago_id' => $this->integer()->unsigned(),
            'accident_id' => $this->integer()->unsigned(),
            'taken_time' => $this->integer()->unsigned(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%neo_request}}');
    }
}
