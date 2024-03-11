<?php

use yii\db\Migration;

/**
 * Class m230424_095557_add_uuid_to_osago_table
 */
class m230424_095557_add_uuid_to_osago_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('osago', 'uuid', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230424_095557_add_uuid_to_osago_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230424_095557_add_uuid_to_osago_table cannot be reverted.\n";

        return false;
    }
    */
}
