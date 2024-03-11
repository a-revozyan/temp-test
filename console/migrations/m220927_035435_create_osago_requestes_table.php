<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%osago_requestes}}`.
 */
class m220927_035435_create_osago_requestes_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%osago_requestes}}', [
            'id' => $this->primaryKey(),
            'url' => $this->string(),
            'request_body' => $this->text(),
            'response_body' => $this->text(),
            'send_date' => $this->integer()->unsigned(),
            'osago_id' => $this->integer()->unsigned(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%osago_requestes}}');
    }
}
