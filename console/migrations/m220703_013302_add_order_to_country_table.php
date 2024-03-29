<?php

use yii\db\Migration;

/**
 * Class m220703_013302_add_order_to_country_table
 */
class m220703_013302_add_order_to_country_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('country', 'order',$this->float());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220703_013302_add_order_to_country_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220703_013302_add_order_to_country_table cannot be reverted.\n";

        return false;
    }
    */
}
