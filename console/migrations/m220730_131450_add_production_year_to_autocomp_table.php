<?php

use yii\db\Migration;

/**
 * Class m220730_131450_add_production_year_to_autocomp_table
 */
class m220730_131450_add_production_year_to_autocomp_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('autocomp', 'production_year', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220730_131450_add_production_year_to_autocomp_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220730_131450_add_production_year_to_autocomp_table cannot be reverted.\n";

        return false;
    }
    */
}
