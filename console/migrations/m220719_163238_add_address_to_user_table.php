<?php

use yii\db\Migration;

/**
 * Class m220719_163238_add_address_to_user_table
 */
class m220719_163238_add_address_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'address', $this->string(65535));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220719_163238_add_address_to_user_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220719_163238_add_address_to_user_table cannot be reverted.\n";

        return false;
    }
    */
}
