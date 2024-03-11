<?php

use yii\db\Migration;

/**
 * Class m230313_110046_add_price_to_travel_table
 */
class m230313_110046_add_price_to_travel_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('travel', 'price', $this->float());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230313_110046_add_price_to_travel_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230313_110046_add_price_to_travel_table cannot be reverted.\n";

        return false;
    }
    */
}
