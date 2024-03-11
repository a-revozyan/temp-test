<?php

use yii\db\Migration;

/**
 * Class m230206_085901_add_comment_to_f_user_table
 */
class m230206_085901_add_comment_to_f_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('f_user', 'comment', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230206_085901_add_comment_to_f_user_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230206_085901_add_comment_to_f_user_table cannot be reverted.\n";

        return false;
    }
    */
}
