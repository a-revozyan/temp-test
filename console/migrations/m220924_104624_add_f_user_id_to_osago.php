<?php

use yii\db\Migration;

/**
 * Class m220924_104624_add_f_user_id_to_osago
 */
class m220924_104624_add_f_user_id_to_osago extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('osago', 'f_user_id', $this->integer()->unsigned()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220924_104624_add_f_user_id_to_osago cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220924_104624_add_f_user_id_to_osago cannot be reverted.\n";

        return false;
    }
    */
}
