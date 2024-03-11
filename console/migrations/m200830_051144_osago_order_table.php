<?php

use yii\db\Migration;

/**
 * Class m200830_051144_osago_order_table
 */
class m200830_051144_osago_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%osago}}', [
            'id' => $this->primaryKey(),
            'partner_id' => $this->integer()->notNull(),
            'autotype_id' => $this->integer()->notNull(),
            'citizenship_id' => $this->integer()->notNull(),
            'period_id' => $this->integer()->notNull(),
            'region_id' => $this->integer()->notNull(),
            'number_drivers_id' => $this->integer()->notNull(),
            'insurer_name' => $this->string()->notNull(),
            'insurer_address' => $this->string()->notNull(),
            'insurer_phone' => $this->string()->notNull(),
            'insurer_passport_series' => $this->string()->notNull(),
            'insurer_passport_number' => $this->string()->notNull(),
            'insurer_tech_pass_series' => $this->string()->notNull(),
            'insurer_tech_pass_number' => $this->string()->notNull(),
            'insurer_pinfl' => $this->string()->notNull(),
            'autonumber' => $this->string()->notNull(),
            'amount_uzs' => $this->float()->notNull(),
            'amount_usd' => $this->float()->notNull(),
            'status' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ], null);

        // FK partner
        $this->createIndex(
            'idx-osago-partner_id',
            'osago',
            'partner_id'
        );

        $this->addForeignKey(
            'fk-osago-partner_id',
            'osago',
            'partner_id',
            'partner',
            'id',
            'RESTRICT'
        );

        // FK autotype
        $this->createIndex(
            'idx-osago-autotype_id',
            'osago',
            'autotype_id'
        );

        $this->addForeignKey(
            'fk-osago-autotype_id',
            'osago',
            'autotype_id',
            'autotype',
            'id',
            'RESTRICT'
        );

        // FK citizenship
        $this->createIndex(
            'idx-osago-citizenship_id',
            'osago',
            'citizenship_id'
        );

        $this->addForeignKey(
            'fk-osago-citizenship_id',
            'osago',
            'citizenship_id',
            'citizenship',
            'id',
            'RESTRICT'
        );

        // FK period
        $this->createIndex(
            'idx-osago-period_id',
            'osago',
            'period_id'
        );

        $this->addForeignKey(
            'fk-osago-period_id',
            'osago',
            'period_id',
            'period',
            'id',
            'RESTRICT'
        );

        // FK region
        $this->createIndex(
            'idx-osago-region_id',
            'osago',
            'region_id'
        );

        $this->addForeignKey(
            'fk-osago-region_id',
            'osago',
            'region_id',
            'region',
            'id',
            'RESTRICT'
        );

        // FK number_drivers
        $this->createIndex(
            'idx-osago-number_drivers_id',
            'osago',
            'number_drivers_id'
        );

        $this->addForeignKey(
            'fk-osago-number_drivers_id',
            'osago',
            'number_drivers_id',
            'number_drivers',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200830_051144_osago_order_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200830_051144_osago_order_table cannot be reverted.\n";

        return false;
    }
    */
}
