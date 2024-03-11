<?php

use yii\db\Migration;

/**
 * Class m201007_190641_transactions
 */
class m201007_190641_transactions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%transaction}}', [
            'id' => $this->primaryKey(),
            'partner_id' => $this->integer()->notNull(),
            'trans_no' => $this->string()->notNull(),
            'amount' => $this->float()->notNull(),
            'trans_date' => $this->date()->notNull(),
            'perform_time' => $this->float(),
            'cancel_time' => $this->float(),
            'create_time' => $this->float()->notNull(),
            'reason' => $this->integer(),
            'payment_type' => $this->string()->notNull(),
            'token' => $this->string(),
            'status' => $this->integer()->notNull(),
        ], null);

        $this->createIndex(
            'idx-transaction-partner_id',
            'transaction',
            'partner_id'
        );

        $this->addForeignKey(
            'fk-transaction-partner_id',
            'transaction',
            'partner_id',
            'partner',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201007_190641_transactions cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201007_190641_transactions cannot be reverted.\n";

        return false;
    }
    */
}
