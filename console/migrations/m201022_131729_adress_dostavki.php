<?php

use yii\db\Migration;

/**
 * Class m201022_131729_adress_dostavki
 */
class m201022_131729_adress_dostavki extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('osago', 'address_delivery', $this->string());
        $this->addColumn('kasko', 'address_delivery', $this->string());
        $this->addColumn('travel', 'address_delivery', $this->string());
         }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201022_131729_adress_dostavki cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201022_131729_adress_dostavki cannot be reverted.\n";

        return false;
    }
    */
}
