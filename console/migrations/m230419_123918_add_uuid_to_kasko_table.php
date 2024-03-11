<?php

use yii\db\Migration;

/**
 * Class m230419_123918_add_uuid_to_kasko_table
 */
class m230419_123918_add_uuid_to_kasko_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('kasko', 'uuid', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230419_123918_add_uuid_to_kasko_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230419_123918_add_uuid_to_kasko_table cannot be reverted.\n";

        return false;
    }
    */
}
