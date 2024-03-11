<?php

use yii\db\Migration;

/**
 * Class m221226_105408_change_card_id_in_save_card_table
 */
class m221226_105408_change_card_id_in_save_card_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('saved_card', 'card_id', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221226_105408_change_card_id_in_save_card_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221226_105408_change_card_id_in_save_card_table cannot be reverted.\n";

        return false;
    }
    */
}
