<?php

use yii\db\Migration;

/**
 * Class m230317_140052_alter_kasko_table
 */
class m230317_140052_alter_kasko_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('kasko', 'tariff_id', $this->integer());
        $this->alterColumn('kasko', 'price', $this->double());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230317_140052_alter_kasko_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230317_140052_alter_kasko_table cannot be reverted.\n";

        return false;
    }
    */
}
