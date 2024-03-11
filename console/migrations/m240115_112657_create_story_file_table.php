<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%story_file}}`.
 */
class m240115_112657_create_story_file_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%story_file}}', [
            'id' => $this->primaryKey(),
            'story_id' => $this->integer(),
            'path' => $this->string(),
            'type' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%story_file}}');
    }
}
