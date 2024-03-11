<?php

use yii\db\Migration;

/**
 * Class m231122_111911_add_user_id_to_token_table
 */
class m231122_111911_add_user_id_to_token_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('token', 'user_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231122_111911_add_user_id_to_token_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231122_111911_add_user_id_to_token_table cannot be reverted.\n";

        return false;
    }
    */
}
