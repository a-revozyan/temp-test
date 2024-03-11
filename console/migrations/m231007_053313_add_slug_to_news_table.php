<?php

use yii\db\Migration;

/**
 * Class m231007_053313_add_slug_to_news_table
 */
class m231007_053313_add_slug_to_news_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('news', 'slug_uz', $this->string(1000));
        $this->addColumn('news', 'slug_ru', $this->string(1000));
        $this->addColumn('news', 'slug_en', $this->string(1000));
        $this->alterColumn('news', 'title_uz', $this->string(1000));
        $this->alterColumn('news', 'title_ru', $this->string(1000));
        $this->alterColumn('news', 'title_en', $this->string(1000));
        $this->alterColumn('news', 'short_info_uz', $this->string(1000));
        $this->alterColumn('news', 'short_info_ru', $this->string(1000));
        $this->alterColumn('news', 'short_info_en', $this->string(1000));
        $this->alterColumn('news', 'body_uz', $this->text());
        $this->alterColumn('news', 'body_ru', $this->text());
        $this->alterColumn('news', 'body_en', $this->text());
        $this->dropColumn('news', 'created_at');
        $this->dropColumn('news', 'updated_at');
        $this->addColumn('news', 'created_at', $this->dateTime());
        $this->addColumn('news', 'updated_at', $this->dateTime());
        $this->addColumn('news', 'is_main', $this->boolean());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231007_053313_add_slug_to_news_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231007_053313_add_slug_to_news_table cannot be reverted.\n";

        return false;
    }
    */
}
