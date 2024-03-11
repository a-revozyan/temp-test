<?php

use yii\db\Migration;

/**
 * Class m230912_062423_add_car_price_telegram_chat_id_to_token_table
 */
class m230912_062423_add_car_price_telegram_chat_id_to_token_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('token', 'car_price_telegram_chat_id', $this->bigInteger());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230912_062423_add_car_price_telegram_chat_id_to_token_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230912_062423_add_car_price_telegram_chat_id_to_token_table cannot be reverted.\n";

        return false;
    }
    */
}
