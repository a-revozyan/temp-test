<?php

use yii\db\Migration;

/**
 * Class m230427_123424_add_user_id_to_partner_table
 */
class m230427_123424_add_user_id_to_partner_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('partner', 'f_user_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230427_123424_add_user_id_to_partner_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230427_123424_add_user_id_to_partner_table cannot be reverted.\n";

        return false;
    }
    */
}
