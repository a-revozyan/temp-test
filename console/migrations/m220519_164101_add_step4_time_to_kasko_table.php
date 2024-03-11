<?php

use yii\db\Migration;

/**
 * Class m220519_164101_add_step4_time_to_kasko_table
 */
class m220519_164101_add_step4_time_to_kasko_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%kasko}}', 'step4_date', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220519_164101_add_step4_time_to_kasko_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220519_164101_add_step4_time_to_kasko_table cannot be reverted.\n";

        return false;
    }
    */
}
