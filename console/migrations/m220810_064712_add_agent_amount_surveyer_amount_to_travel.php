<?php

use yii\db\Migration;

/**
 * Class m220810_064712_add_agent_amount_surveyer_amount_to_travel
 */
class m220810_064712_add_agent_amount_surveyer_amount_to_travel extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('travel', 'agent_amount', $this->integer());
        $this->addColumn('travel', 'surveyer_amount', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220810_064712_add_agent_amount_surveyer_amount_to_travel cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220810_064712_add_agent_amount_surveyer_amount_to_travel cannot be reverted.\n";

        return false;
    }
    */
}
