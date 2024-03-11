<?php

use yii\db\Migration;

/**
 * Class m220608_191239_make_nullable_some_cols_to_travel_table
 */
class m220608_191239_make_nullable_some_cols_to_travel_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('travel', 'partner_id', $this->integer());
        $this->alterColumn('travel', 'program_id', $this->integer());
        $this->alterColumn('travel', 'amount_uzs', $this->double());
        $this->alterColumn('travel', 'amount_usd', $this->double());
        $this->alterColumn('travel', 'insurer_name', $this->string());
        $this->alterColumn('travel', 'insurer_phone', $this->string());
        $this->alterColumn('travel', 'insurer_passport_series', $this->string());
        $this->alterColumn('travel', 'insurer_passport_number', $this->string());
        $this->alterColumn('travel', 'insurer_birthday', $this->date());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220608_191239_make_nullable_some_cols_to_travel_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220608_191239_make_nullable_some_cols_to_travel_table cannot be reverted.\n";

        return false;
    }
    */
}
