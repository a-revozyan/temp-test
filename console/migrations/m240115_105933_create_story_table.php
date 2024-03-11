<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%story}}`.
 */
class m240115_105933_create_story_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%story}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(1000),
            'begin_period' => $this->date(),
            'end_period' => $this->date(),
            'weekdays' => 'JSONB',
            'begin_time' => $this->time(),
            'end_time' => $this->time(),
            'priority' => $this->integer(),
            'view_condition' => $this->integer(),
            'type' => $this->integer(),
            'status' => $this->integer(),
            'created_at' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%story}}');
    }
}
