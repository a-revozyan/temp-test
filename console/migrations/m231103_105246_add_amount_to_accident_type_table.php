<?php

use yii\db\Migration;

/**
 * Class m231103_105246_add_amount_to_accident_type_table
 */
class m231103_105246_add_amount_to_accident_type_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('accident_type', 'amount', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231103_105246_add_amount_to_accident_type_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231103_105246_add_amount_to_accident_type_table cannot be reverted.\n";

        return false;
    }
    */
}
