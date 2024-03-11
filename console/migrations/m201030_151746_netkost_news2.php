<?php

use yii\db\Migration;

/**
 * Class m201030_151746_netkost_news2
 */
class m201030_151746_netkost_news2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;

        $this->createTable('{{%news}}', [
            'id' => $this->primaryKey(),
            'title_ru' => $this->string()->notNull(),
            'title_uz' => $this->string()->notNull(),
            'title_en' => $this->string()->notNull(),
            'image_ru' => $this->string(),
            'image_uz' => $this->string(),
            'image_en' => $this->string(),
            'short_info_ru' => $this->string()->notNull(),
            'short_info_uz' => $this->string()->notNull(),
            'short_info_en' => $this->string()->notNull(),
            'body_ru' => $this->text()->notNull(),
            'body_uz' => $this->text()->notNull(),
            'body_en' => $this->text()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201030_151746_netkost_news2 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201030_151746_netkost_news2 cannot be reverted.\n";

        return false;
    }
    */
}
