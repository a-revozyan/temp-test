<?php

use yii\db\Migration;

/**
 * Class m231120_132630_add_travel_offer_file_to_partner_table
 */
class m231120_132630_add_travel_offer_file_to_partner_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('partner', 'travel_offer_file', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231120_132630_add_travel_offer_file_to_partner_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231120_132630_add_travel_offer_file_to_partner_table cannot be reverted.\n";

        return false;
    }
    */
}
