<?php

use yii\db\Migration;

/**
 * Class m231114_062105_add_authorization_to_bridge_company_table
 */
class m231114_062105_add_authorization_to_bridge_company_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('bridge_company', 'authorization', $this->string(1000));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231114_062105_add_authorization_to_bridge_company_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231114_062105_add_authorization_to_bridge_company_table cannot be reverted.\n";

        return false;
    }
    */
}
