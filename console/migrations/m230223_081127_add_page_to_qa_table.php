<?php

use yii\db\Migration;

/**
 * Class m230223_081127_add_page_to_qa_table
 */
class m230223_081127_add_page_to_qa_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('qa', 'page', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230223_081127_add_page_to_qa_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230223_081127_add_page_to_qa_table cannot be reverted.\n";

        return false;
    }
    */
}
