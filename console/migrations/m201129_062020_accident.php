<?php

use yii\db\Migration;

/**
 * Class m201129_062020_accident
 */
class m201129_062020_accident extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('accident_partner_program', [
            'id' => $this->primaryKey(),
            'partner_id' => $this->integer()->notNull(),
            'insurance_amount_from' => $this->float()->notNull(),
            'insurance_amount_to' => $this->float()->notNull(),
            'percent' => $this->float()->notNull()
        ], null);

        $this->createIndex(
            'idx-accident_partner_program-partner_id',
            'accident_partner_program',
            'partner_id'
        );

        $this->addForeignKey(
            'fk-accident_partner_program-partner_id',
            'accident_partner_program',
            'partner_id',
            'partner',
            'id',
            'RESTRICT'
        );

        $this->createTable('accident', [
            'id' => $this->primaryKey(),
            'partner_id' => $this->integer()->notNull(),
            'insurer_name' => $this->string()->notNull(),
            'insurer_birthday' => $this->date()->notNull(),
            'insurer_passport_file' => $this->string()->notNull(),
            'insurer_phone' => $this->string()->notNull(),
            'insurer_email' => $this->string(),
            'address_delivery' => $this->string(),
            'insurance_amount' => $this->float()->notNull(),
            'amount_uzs' => $this->float()->notNull(),
            'amount_usd' => $this->float()->notNull(),
            'trans_id' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'status' => $this->integer()->notNull(),
            'viewed' => $this->boolean()->notNull(),
            'policy_order' => $this->integer(),
            'policy_number' => $this->string()
        ], null);

        $this->createIndex(
            'idx-accident-partner_id',
            'accident',
            'partner_id'
        );

        $this->addForeignKey(
            'fk-accident-partner_id',
            'accident',
            'partner_id',
            'partner',
            'id',
            'RESTRICT'
        );

        $this->createIndex(
            'idx-accident-trans_id',
            'accident',
            'trans_id'
        );

        $this->addForeignKey(
            'fk-accident-trans_id',
            'accident',
            'trans_id',
            'transaction',
            'id',
            'RESTRICT'
        );

        $this->createTable('accident_insurer', [
            'id' => $this->primaryKey(),
            'accident_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'birthday' => $this->date()->notNull(),
            'passport_file' => $this->string()->notNull()
        ], null);

        $this->createIndex(
            'idx-accident_insurer-accident_id',
            'accident_insurer',
            'accident_id'
        );

        $this->addForeignKey(
            'fk-accident_insurer-accident_id',
            'accident_insurer',
            'accident_id',
            'accident',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201129_062020_accident cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201129_062020_accident cannot be reverted.\n";

        return false;
    }
    */
}
