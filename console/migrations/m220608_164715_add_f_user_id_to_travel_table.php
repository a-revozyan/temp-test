<?php

use yii\db\Migration;

/**
 * Class m220608_164715_add_f_user_id_to_travel_table
 */
class m220608_164715_add_f_user_id_to_travel_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('travel', 'f_user_id', $this->integer());
        $this->addColumn('travel', 'is_multiple', $this->integer());
        $this->addColumn('travel', 'has_covid', $this->integer());

        $this->addForeignKey(
            'fk-travel-f_user_id',
            'travel',
            'f_user_id',
            'f_user',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220608_164715_add_f_user_id_to_travel_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220608_164715_add_f_user_id_to_travel_table cannot be reverted.\n";

        return false;
    }
    */
}
