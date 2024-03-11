<?php

use yii\db\Migration;

/**
 * Class m220730_051456_add_contract_number_to_partner
 */
class m220730_051456_add_contract_number_to_partner extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('partner', 'contract_number', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220730_051456_add_contract_number_to_partner cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220730_051456_add_contract_number_to_partner cannot be reverted.\n";

        return false;
    }
    */
}
