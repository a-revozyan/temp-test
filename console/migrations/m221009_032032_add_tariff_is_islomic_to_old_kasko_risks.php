<?php

use yii\db\Migration;

/**
 * Class m221009_032032_add_tariff_is_islomic_to_old_kasko_risks
 */
class m221009_032032_add_tariff_is_islomic_to_old_kasko_risks extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('old_kasko_risk', 'tariff_is_islomic', $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221009_032032_add_tariff_is_islomic_to_old_kasko_risks cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221009_032032_add_tariff_is_islomic_to_old_kasko_risks cannot be reverted.\n";

        return false;
    }
    */
}
