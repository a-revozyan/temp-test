<?php

use yii\db\Migration;

/**
 * Class m220618_063903_add_name_passport_series_passport_number_birthday_to_travel_member
 */
class m220618_063903_add_name_passport_series_passport_number_birthday_to_travel_member extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('travel_member', 'name', $this->string());
        $this->addColumn('travel_member', 'passport_series', $this->string());
        $this->addColumn('travel_member', 'passport_number', $this->string());
        $this->addColumn('travel_member', 'birthday', $this->date());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220618_063903_add_name_passport_series_passport_number_birthday_to_travel_member cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220618_063903_add_name_passport_series_passport_number_birthday_to_travel_member cannot be reverted.\n";

        return false;
    }
    */
}
