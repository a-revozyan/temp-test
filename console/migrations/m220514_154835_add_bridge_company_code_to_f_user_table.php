<?php

use yii\db\Migration;

/**
 * Class m220514_154835_add_bridge_company_code_to_f_user_table
 */
class m220514_154835_add_bridge_company_code_to_f_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("f_user", "bridge_company_id", $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220514_154835_add_bridge_company_code_to_f_user_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220514_154835_add_bridge_company_code_to_f_user_table cannot be reverted.\n";

        return false;
    }
    */
}
