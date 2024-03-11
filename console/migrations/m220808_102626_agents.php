<?php

use yii\db\Migration;

/**
 * Class m220808_102626_agents
 */
class m220808_102626_agents extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('agent', [
            'id' => $this->primaryKey(),
            'f_user_id' => $this->integer(),
            'contract_number' => $this->string(),
            'logo' => $this->string(),
            'inn' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220808_102626_agents cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220808_102626_agents cannot be reverted.\n";

        return false;
    }
    */
}
