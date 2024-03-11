<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%f_user_story_view}}`.
 */
class m240115_122814_create_f_user_story_view_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%f_user_story_view}}', [
            'id' => $this->primaryKey(),
            'f_user_id' => $this->integer(),
            'story_id' => $this->integer(),
            'viewed_at' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%f_user_story_view}}');
    }
}
