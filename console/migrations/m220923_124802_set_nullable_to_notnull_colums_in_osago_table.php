<?php

use yii\db\Migration;

/**
 * Class m220923_124802_set_nullable_to_notnull_colums_in_osago_table
 */
class m220923_124802_set_nullable_to_notnull_colums_in_osago_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('osago', 'partner_id', $this->integer()->unsigned()->null());
        $this->alterColumn('osago', 'autotype_id', $this->integer()->unsigned()->null());
        $this->alterColumn('osago', 'citizenship_id', $this->integer()->unsigned()->null());
        $this->alterColumn('osago', 'period_id', $this->integer()->unsigned()->null());
        $this->alterColumn('osago', 'region_id', $this->integer()->unsigned()->null());
        $this->alterColumn('osago', 'number_drivers_id', $this->integer()->unsigned()->null());
        $this->alterColumn('osago', 'insurer_name', $this->string()->null());
        $this->alterColumn('osago', 'insurer_address', $this->string()->null());
        $this->alterColumn('osago', 'insurer_phone', $this->string()->null());
        $this->alterColumn('osago', 'insurer_passport_series', $this->string()->null());
        $this->alterColumn('osago', 'insurer_passport_number', $this->string()->null());
        $this->alterColumn('osago', 'insurer_pinfl', $this->string()->null());
        $this->alterColumn('osago', 'amount_uzs', $this->double()->null());
        $this->alterColumn('osago', 'amount_usd', $this->double()->null());
        $this->alterColumn('osago', 'address_delivery', $this->string()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220923_124802_set_nullable_to_notnull_colums_in_osago_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220923_124802_set_nullable_to_notnull_colums_in_osago_table cannot be reverted.\n";

        return false;
    }
    */
}
