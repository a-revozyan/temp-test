<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%status_history}}`.
 */
class m230519_052831_create_status_history_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%status_history}}', [
            'id' => $this->primaryKey(),
            'model_class' => $this->string(),
            'model_id' => $this->bigInteger()->unsigned(),
            'from_status' => $this->integer(),
            'to_status' => $this->integer(),
            'created_at' => $this->dateTime(),
            'user_id' => $this->integer(),
            'comment' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%status_history}}');
    }
}
