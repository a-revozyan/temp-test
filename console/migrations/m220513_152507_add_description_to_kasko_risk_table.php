<?php

use yii\db\Migration;

/**
 * Class m220513_152507_add_description_to_kasko_risk_table
 */
class m220513_152507_add_description_to_kasko_risk_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("kasko_risk", "description", $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220513_152507_add_description_to_kasko_risk_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220513_152507_add_description_to_kasko_risk_table cannot be reverted.\n";

        return false;
    }
    */
}
