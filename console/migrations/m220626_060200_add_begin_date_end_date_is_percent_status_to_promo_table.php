<?php

use yii\db\Migration;

/**
 * Class m220626_060200_add_begin_date_end_date_is_percent_status_to_promo_table
 */
class m220626_060200_add_begin_date_end_date_is_percent_status_to_promo_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('promo', 'begin_date', $this->date());
        $this->addColumn('promo', 'end_date', $this->date());
        $this->addColumn('promo', 'amount_type', $this->integer());
        $this->renameColumn('promo', 'percent', 'amount');
        $this->addColumn('promo', 'status', $this->integer());
        $this->addColumn('promo', 'number', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220626_060200_add_begin_date_end_date_is_percent_status_to_promo_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220626_060200_add_begin_date_end_date_is_percent_status_to_promo_table cannot be reverted.\n";

        return false;
    }
    */
}
