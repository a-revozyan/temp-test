<?php

use yii\db\Migration;

/**
 * Class m220808_113227_agent_product_coeff
 */
class m220808_113227_agent_product_coeff extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('agent_product_coeff', [
            'id' => $this->primaryKey(),
            'agent_id' => $this->integer(),
            'product_id' => $this->integer(),
            'coeff' => $this->double(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220808_113227_agent_product_coeff cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220808_113227_agent_product_coeff cannot be reverted.\n";

        return false;
    }
    */
}
