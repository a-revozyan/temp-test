<?php

use yii\db\Migration;

/**
 * Class m231128_105826_alter_only_kapital_to_partner_ability_in_osago_table
 */
class m231128_105826_alter_only_kapital_to_partner_ability_in_osago_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('osago', 'only_kapital', 'partner_ability');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231128_105826_alter_only_kapital_to_partner_ability_in_osago_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231128_105826_alter_only_kapital_to_partner_ability_in_osago_table cannot be reverted.\n";

        return false;
    }
    */
}
