<?php

use yii\db\Migration;

/**
 * Class m200828_054455_product_percent
 */
class m200828_054455_product_percent extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('partner_product', 'percent', $this->float());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200828_054455_product_percent cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200828_054455_product_percent cannot be reverted.\n";

        return false;
    }
    */
}
