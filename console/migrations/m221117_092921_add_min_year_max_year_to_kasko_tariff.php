<?php

use yii\db\Migration;

/**
 * Class m221117_092921_add_min_year_max_year_to_kasko_tariff
 */
class m221117_092921_add_min_year_max_year_to_kasko_tariff extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('kasko_tariff', 'min_year', $this->integer());
        $this->addColumn('kasko_tariff', 'max_year', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221117_092921_add_min_year_max_year_to_kasko_tariff cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221117_092921_add_min_year_max_year_to_kasko_tariff cannot be reverted.\n";

        return false;
    }
    */
}
