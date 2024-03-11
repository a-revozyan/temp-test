<?php

use yii\db\Migration;

/**
 * Class m231101_095118_add_city_id_to_f_user_table
 */
class m231101_095118_add_city_id_to_f_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('f_user', 'city_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231101_095118_add_city_id_to_f_user_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231101_095118_add_city_id_to_f_user_table cannot be reverted.\n";

        return false;
    }
    */
}
