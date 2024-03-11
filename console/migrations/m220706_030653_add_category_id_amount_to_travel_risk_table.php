<?php

use yii\db\Migration;

/**
 * Class m220706_030653_add_category_id_amount_to_travel_risk_table
 */
class m220706_030653_add_category_id_amount_to_travel_risk_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('travel_risk', 'category_id', $this->integer());
        $this->addColumn('travel_risk', 'amount', $this->float());

        $this->addForeignKey(
            'fk-travel_risk-category_id',
            'travel_risk',
            'category_id',
            'travel_risk_category',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220706_030653_add_category_id_amount_to_travel_risk_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220706_030653_add_category_id_amount_to_travel_risk_table cannot be reverted.\n";

        return false;
    }
    */
}
