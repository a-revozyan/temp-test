<?php

use yii\db\Migration;

/**
 * Class m231016_065643_remove_super_agent_id_from_f_user
 */
class m231016_065643_remove_super_agent_id_from_f_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('f_user', 'super_agent_id');
        $this->renameColumn('osago', 'super_agent_id', 'bridge_company_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231016_065643_remove_super_agent_id_from_f_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231016_065643_remove_super_agent_id_from_f_user cannot be reverted.\n";

        return false;
    }
    */
}
