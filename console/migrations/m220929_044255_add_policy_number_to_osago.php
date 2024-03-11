<?php

use yii\db\Migration;

/**
 * Class m220929_044255_add_policy_number_to_osago
 */
class m220929_044255_add_policy_number_to_osago extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('osago', 'policy_number', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220929_044255_add_policy_number_to_osago cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220929_044255_add_policy_number_to_osago cannot be reverted.\n";

        return false;
    }
    */
}
