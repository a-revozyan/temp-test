<?php

use yii\db\Migration;

/**
 * Class m231013_052629_add_super_agent_id_to_f_user_table
 */
class m231013_052629_add_super_agent_id_to_f_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('f_user', 'super_agent_id', $this->integer());
        $this->addColumn('osago', 'super_agent_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231013_052629_add_super_agent_id_to_f_user_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231013_052629_add_super_agent_id_to_f_user_table cannot be reverted.\n";

        return false;
    }
    */
}
