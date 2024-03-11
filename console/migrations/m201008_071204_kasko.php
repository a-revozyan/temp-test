<?php

use yii\db\Migration;

/**
 * Class m201008_071204_kasko
 */
class m201008_071204_kasko extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->createTable('{{%autobrand}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
        ], null);

        $this->createTable('{{%automodel}}', [
            'id' => $this->primaryKey(),
            'autobrand_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
        ], null);

        $this->createIndex(
            'idx-automodel-autobrand_id',
            'automodel',
            'autobrand_id'
        );

        $this->addForeignKey(
            'fk-automodel-autobrand_id',
            'automodel',
            'autobrand_id',
            'autobrand',
            'id',
            'RESTRICT'
        );


        $this->createTable('{{%autocomp}}', [
            'id' => $this->primaryKey(),
            'automodel_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'price' => $this->float()->notNull(),
        ], null);


        $this->createIndex(
            'idx-autocomp-automodel_id',
            'autocomp',
            'automodel_id'
        );

        $this->addForeignKey(
            'fk-autocomp-automodel_id',
            'autocomp',
            'automodel_id',
            'automodel',
            'id',
            'RESTRICT'
        );

        $this->createTable('{{%kasko_risk}}', [
            'id' => $this->primaryKey(),
            'name_ru' => $this->string()->notNull(),
            'name_uz' => $this->string()->notNull(),
            'name_en' => $this->string()->notNull(),
        ], null);

        $this->createTable('{{%kasko_tariff}}', [
            'id' => $this->primaryKey(),
            'partner_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'amount_kind' => $this->string()->notNull(),
            'amount' => $this->float()->notNull(),
        ], null);

        $this->createIndex(
            'idx-kasko_tariff-partner_id',
            'kasko_tariff',
            'partner_id'
        );

        $this->addForeignKey(
            'fk-kasko_tariff-partner_id',
            'kasko_tariff',
            'partner_id',
            'partner',
            'id',
            'RESTRICT'
        );

        $this->createTable('{{%kasko_tariff_risk}}', [
            'id' => $this->primaryKey(),
            'tariff_id' => $this->integer()->notNull(),
            'risk_id' => $this->integer()->notNull(),
        ], null);

        $this->createIndex(
            'idx-kasko_tariff_risk-tariff_id',
            'kasko_tariff_risk',
            'tariff_id'
        );

        $this->addForeignKey(
            'fk-kasko_tariff_risk-tariff_id',
            'kasko_tariff_risk',
            'tariff_id',
            'kasko_tariff',
            'id',
            'RESTRICT'
        );

        $this->createIndex(
            'idx-kasko_tariff_risk-risk_id',
            'kasko_tariff_risk',
            'risk_id'
        );

        $this->addForeignKey(
            'fk-kasko_tariff_risk-risk_id',
            'kasko_tariff_risk',
            'risk_id',
            'kasko_risk',
            'id',
            'RESTRICT'
        );

        $this->createTable('{{%kasko}}', [
            'id' => $this->primaryKey(),
            'tariff_id' => $this->integer()->notNull(),
            'autocomp_id' => $this->integer()->notNull(),
            'year' => $this->integer()->notNull(),
            'price' => $this->float()->notNull(),
            'autonumber' => $this->string()->notNull(),
            'amount_uzs' => $this->float()->notNull(),
            'amount_usd' => $this->float()->notNull(),
            'begin_date' => $this->date()->notNull(),
            'end_date' => $this->date()->notNull(),
            'status' => $this->integer()->notNull(),
            'trans_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'insurer_name' => $this->string()->notNull(),
            'insurer_address' => $this->string()->notNull(),
            'insurer_phone' => $this->string()->notNull(),
            'insurer_passport_series' => $this->string()->notNull(),
            'insurer_passport_number' => $this->string()->notNull(),
            'insurer_tech_pass_series' => $this->string()->notNull(),
            'insurer_tech_pass_number' => $this->string()->notNull(),
            'insurer_pinfl' => $this->string()->notNull(),
        ], null);

        $this->createIndex(
            'idx-kasko-tariff_id',
            'kasko',
            'tariff_id'
        );

        $this->addForeignKey(
            'fk-kasko-tariff_id',
            'kasko',
            'tariff_id',
            'kasko_tariff',
            'id',
            'RESTRICT'
        );

        $this->createIndex(
            'idx-kasko-autocomp_id',
            'kasko',
            'autocomp_id'
        );

        $this->addForeignKey(
            'fk-kasko-autocomp_id',
            'kasko',
            'autocomp_id',
            'autocomp',
            'id',
            'RESTRICT'
        );

        $this->createIndex(
            'idx-kasko-trans_id',
            'kasko',
            'trans_id'
        );

        $this->addForeignKey(
            'fk-kasko-trans_id',
            'kasko',
            'trans_id',
            'transaction',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201008_071204_kasko cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201008_071204_kasko cannot be reverted.\n";

        return false;
    }
    */
}
