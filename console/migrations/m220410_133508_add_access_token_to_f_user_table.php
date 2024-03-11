<?php

use yii\db\Migration;

/**
 * Class m220410_133508_add_access_token_to_f_user_table
 */
class m220410_133508_add_access_token_to_f_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%f_user}}', 'access_token', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220410_133508_add_access_token_to_f_user_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220410_133508_add_access_token_to_f_user_table cannot be reverted.\n";

        return false;
    }
    */
}
