<?php

use yii\db\Migration;

/**
 * Class m230921_100016_add_new_phone_number_to_token_table
 */
class m230921_100016_add_new_phone_number_to_token_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('token', 'new_phone_number', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230921_100016_add_new_phone_number_to_token_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230921_100016_add_new_phone_number_to_token_table cannot be reverted.\n";

        return false;
    }
    */
}
