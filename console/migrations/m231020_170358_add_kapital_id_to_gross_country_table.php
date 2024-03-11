<?php

use yii\db\Migration;

/**
 * Class m231020_170358_add_kapital_id_to_gross_country_table
 */
class m231020_170358_add_kapital_id_to_gross_country_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('gross_country', 'kapital_id', $this->integer());
        $this->addColumn('country', 'kapital_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231020_170358_add_kapital_id_to_gross_country_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231020_170358_add_kapital_id_to_gross_country_table cannot be reverted.\n";

        return false;
    }
    */
}
