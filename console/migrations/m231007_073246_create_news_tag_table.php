<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%news_tag}}`.
 */
class m231007_073246_create_news_tag_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%news_tag}}', [
            'id' => $this->primaryKey(),
            'news_id' => $this->integer()->unsigned(),
            'tag_id' => $this->integer()->unsigned(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%news_tag}}');
    }
}
