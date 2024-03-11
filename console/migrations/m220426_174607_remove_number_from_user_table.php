<?php

use yii\db\Migration;

/**
 * Class m220426_174607_remove_number_from_user_table
 */
class m220426_174607_remove_number_from_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('user', 'number', "access_token");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220426_174607_remove_number_from_user_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220426_174607_remove_number_from_user_table cannot be reverted.\n";

        return false;
    }
    */
}
