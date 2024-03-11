<?php

use yii\db\Migration;

/**
 * Class m220808_110202_agent_files
 */
class m220808_110202_agent_files extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('agent_files', [
           'id' => $this->primaryKey(),
           'agent_id' => $this->integer(),
           'path' => $this->string()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220808_110202_agent_files cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220808_110202_agent_files cannot be reverted.\n";

        return false;
    }
    */
}
