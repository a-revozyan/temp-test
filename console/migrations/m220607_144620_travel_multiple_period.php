<?php

use yii\db\Migration;

/**
 * Class m220607_144620_travel_multiple_period
 */
class m220607_144620_travel_multiple_period extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%travel_multiple_period}}', [
            'id' => $this->primaryKey(),
            'partner_id' => $this->integer(),
            'program_id' => $this->integer(),
            'available_interval_days' => $this->integer(), // policy qancha kun amal qiladi
            'days' => $this->integer(),  // available_interval_days ning ichidan bo'lib bo'lib necha kun sayohatga chiqishi
            'amount' => $this->double()
        ], null);

        $this->addForeignKey(
            'fk-travel_multiple_period-partner_id',
            'travel_multiple_period',
            'partner_id',
            'partner',
            'id',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-travel_multiple_period-program_id',
            'travel_multiple_period',
            'program_id',
            'travel_program',
            'id',
            'RESTRICT'
        );
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220607_144620_travel_multiple_period cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220607_144620_travel_multiple_period cannot be reverted.\n";

        return false;
    }
    */
}
