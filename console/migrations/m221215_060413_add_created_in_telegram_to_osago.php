<?php

use yii\db\Migration;

/**
 * Class m221215_060413_add_created_in_telegram_to_osago
 */
class m221215_060413_add_created_in_telegram_to_osago extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('osago', 'created_in_telegram', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221215_060413_add_created_in_telegram_to_osago cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221215_060413_add_created_in_telegram_to_osago cannot be reverted.\n";

        return false;
    }
    */
}
