<?php

use yii\db\Migration;

/**
 * Class m220511_155950_add_order_to_automodel
 */
class m220511_155950_add_order_to_automodel extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('automodel', 'order',$this->float());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220511_155950_add_order_to_automodel cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220511_155950_add_order_to_automodel cannot be reverted.\n";

        return false;
    }
    */
}
