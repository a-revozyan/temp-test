<?php

use yii\db\Migration;

/**
 * Class m220622_130516_add_price_to_program
 */
class m220622_130516_add_price_to_program extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('travel_program', 'price', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220622_130516_add_price_to_program cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220622_130516_add_price_to_program cannot be reverted.\n";

        return false;
    }
    */
}
