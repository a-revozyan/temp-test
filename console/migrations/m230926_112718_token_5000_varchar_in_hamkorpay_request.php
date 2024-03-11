<?php

use yii\db\Migration;

/**
 * Class m230926_112718_token_5000_varchar_in_hamkorpay_request
 */
class m230926_112718_token_5000_varchar_in_hamkorpay_request extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('hamkorpay_request', 'token', $this->string(5000));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230926_112718_token_5000_varchar_in_hamkorpay_request cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230926_112718_token_5000_varchar_in_hamkorpay_request cannot be reverted.\n";

        return false;
    }
    */
}
