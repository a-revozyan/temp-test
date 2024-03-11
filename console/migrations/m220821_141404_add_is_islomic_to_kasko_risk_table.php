<?php

use yii\db\Migration;

/**
 * Class m220821_141404_add_is_islomic_to_kasko_risk_table
 */
class m220821_141404_add_is_islomic_to_kasko_risk_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('kasko_tariff', 'is_islomic', $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220821_141404_add_is_islomic_to_kasko_risk_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220821_141404_add_is_islomic_to_kasko_risk_table cannot be reverted.\n";

        return false;
    }
    */
}
