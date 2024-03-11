<?php

use yii\db\Migration;

/**
 * Class m201028_201148_partner_star
 */
class m201028_201148_partner_star extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('partner_product', 'star', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201028_201148_partner_star cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201028_201148_partner_star cannot be reverted.\n";

        return false;
    }
    */
}
