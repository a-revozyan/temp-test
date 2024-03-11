<?php

use yii\db\Migration;

/**
 * Class m231024_054526_add_pinfl_first_name_last_name_to_travel_member
 */
class m231024_054526_add_pinfl_first_name_last_name_to_travel_member extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('travel_member', 'first_name', $this->string());
        $this->addColumn('travel_member', 'last_name', $this->string());
        $this->addColumn('travel_member', 'pinfl', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231024_054526_add_pinfl_first_name_last_name_to_travel_member cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231024_054526_add_pinfl_first_name_last_name_to_travel_member cannot be reverted.\n";

        return false;
    }
    */
}
