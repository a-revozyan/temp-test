<?php

use yii\db\Migration;

/**
 * Class m220511_155936_add_order_to_autobrand
 */
class m220511_155936_add_order_to_autobrand extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('autobrand', 'order',$this->float());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220511_155936_add_order_to_autobrand cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220511_155936_add_order_to_autobrand cannot be reverted.\n";

        return false;
    }
    */
}
