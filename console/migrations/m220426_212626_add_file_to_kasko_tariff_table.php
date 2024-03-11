<?php

use yii\db\Migration;

/**
 * Class m220426_212626_add_file_to_kasko_tariff_table
 */
class m220426_212626_add_file_to_kasko_tariff_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("kasko_tariff", "file", $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220426_212626_add_file_to_kasko_tariff_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220426_212626_add_file_to_kasko_tariff_table cannot be reverted.\n";

        return false;
    }
    */
}
