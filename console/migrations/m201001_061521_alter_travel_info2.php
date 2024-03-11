<?php

use yii\db\Migration;

/**
 * Class m201001_061521_alter_travel_info2
 */
class m201001_061521_alter_travel_info2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->alterColumn('travel_partner_info', 'rules', $this->string());
        $this->alterColumn('travel_partner_info', 'policy_example', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201001_061521_alter_travel_info2 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201001_061521_alter_travel_info2 cannot be reverted.\n";

        return false;
    }
    */
}
