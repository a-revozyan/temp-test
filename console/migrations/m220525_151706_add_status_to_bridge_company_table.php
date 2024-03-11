<?php

use yii\db\Migration;

/**
 * Class m220525_151706_add_status_to_bridge_company_table
 */
class m220525_151706_add_status_to_bridge_company_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('bridge_company', 'status', $this->smallInteger()->notNull()->defaultValue(10));
        $this->addColumn('bridge_company', 'user_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220525_151706_add_status_to_bridge_company_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220525_151706_add_status_to_bridge_company_table cannot be reverted.\n";

        return false;
    }
    */
}
