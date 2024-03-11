<?php

use yii\db\Migration;

/**
 * Class m201124_144728_url_counter
 */
class m201124_144728_url_counter extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%url_counter}}', [
            'id' => $this->primaryKey(),
            'url' => $this->string()->notNull(),
            'code' => $this->string()->notNull(),
            'count' => $this->integer()->defaultValue(0)->notNull(),
        ], null);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201124_144728_url_counter cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201124_144728_url_counter cannot be reverted.\n";

        return false;
    }
    */
}
