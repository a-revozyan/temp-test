<?php

use yii\db\Migration;

/**
 * Class m200905_114054_travel_refs
 */
class m200905_114054_travel_refs extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%country}}', [
            'id' => $this->primaryKey(),
            'name_ru' => $this->string()->notNull(),
            'name_uz' => $this->string()->notNull(),
            'name_en' => $this->string()->notNull(),
            'schengen' => $this->boolean()->notNull(),
            'parent_id' => $this->integer(),
            'code' => $this->string(),
            'image' => $this->string(),
        ], null);

        $this->createIndex(
            'idx-country-parent_id',
            'country',
            'parent_id'
        );

        $this->addForeignKey(
            'fk-country-parent_id',
            'country',
            'parent_id',
            'country',
            'id',
            'RESTRICT'
        );

        $this->createTable('{{%travel_program}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'partner_id' => $this->integer()->notNull(),
            'status' => $this->boolean()->notNull(),
        ], null);

        $this->createIndex(
            'idx-travel_program-partner_id',
            'travel_program',
            'partner_id'
        );

        $this->addForeignKey(
            'fk-travel_program-partner_id',
            'travel_program',
            'partner_id',
            'partner',
            'id',
            'RESTRICT'
        );

        $this->createTable('{{%travel_program_country}}', [
            'id' => $this->primaryKey(),
            'partner_id' => $this->integer()->notNull(),
            'program_id' => $this->integer()->notNull(),
            'country_id' => $this->integer()->notNull(),
        ], null);

        $this->createIndex(
            'idx-travel_program_country-partner_id',
            'travel_program_country',
            'partner_id'
        );

        $this->addForeignKey(
            'fk-travel_program_country-partner_id',
            'travel_program_country',
            'partner_id',
            'partner',
            'id',
            'RESTRICT'
        );

        $this->createIndex(
            'idx-travel_program_country-program_id',
            'travel_program_country',
            'program_id'
        );

        $this->addForeignKey(
            'fk-travel_program_country-program_id',
            'travel_program_country',
            'program_id',
            'travel_program',
            'id',
            'RESTRICT'
        );

        $this->createIndex(
            'idx-travel_program_country-country_id',
            'travel_program_country',
            'country_id'
        );

        $this->addForeignKey(
            'fk-travel_program_country-country_id',
            'travel_program_country',
            'country_id',
            'country',
            'id',
            'RESTRICT'
        );

        $this->createTable('{{%travel_purpose}}', [
            'id' => $this->primaryKey(),
            'partner_id' => $this->integer()->notNull(),
            'name_ru' => $this->string()->notNull(),
            'name_uz' => $this->string()->notNull(),
            'name_en' => $this->string()->notNull(),
            'coeff' => $this->float()->notNull()->defaultValue(1),
            'status' => $this->boolean()->notNull(),
        ], null);

        $this->createIndex(
            'idx-travel_purpose-partner_id',
            'travel_purpose',
            'partner_id'
        );

        $this->addForeignKey(
            'fk-travel_purpose-partner_id',
            'travel_purpose',
            'partner_id',
            'partner',
            'id',
            'RESTRICT'
        );

        $this->createTable('{{%travel_group_type}}', [
            'id' => $this->primaryKey(),
            'partner_id' => $this->integer()->notNull(),
            'name_ru' => $this->string()->notNull(),
            'name_uz' => $this->string()->notNull(),
            'name_en' => $this->string()->notNull(),
            'coeff' => $this->float()->notNull()->defaultValue(1),
            'status' => $this->boolean()->notNull(),
        ], null);

        $this->createIndex(
            'idx-travel_group_type-partner_id',
            'travel_group_type',
            'partner_id'
        );

        $this->addForeignKey(
            'fk-travel_group_type-partner_id',
            'travel_group_type',
            'partner_id',
            'partner',
            'id',
            'RESTRICT'
        );

        $this->createTable('{{%travel_risk}}', [
            'id' => $this->primaryKey(),
            'partner_id' => $this->integer()->notNull(),
            'name_ru' => $this->string()->notNull(),
            'name_uz' => $this->string()->notNull(),
            'name_en' => $this->string()->notNull(),
            'status' => $this->boolean()->notNull(),
        ], null);
        
        $this->createIndex(
            'idx-travel_risk-partner_id',
            'travel_risk',
            'partner_id'
        );

        $this->addForeignKey(
            'fk-travel_risk-partner_id',
            'travel_risk',
            'partner_id',
            'partner',
            'id',
            'RESTRICT'
        );

        $this->createTable('{{%travel_program_risk}}', [
            'id' => $this->primaryKey(),
            'partner_id' => $this->integer()->notNull(),
            'program_id' => $this->integer()->notNull(),
            'risk_id' => $this->integer()->notNull(),
            'amount' => $this->float()->notNull(),
        ], null);
        
        $this->createIndex(
            'idx-travel_program_risk-partner_id',
            'travel_program_risk',
            'partner_id'
        );

        $this->addForeignKey(
            'fk-travel_program_risk-partner_id',
            'travel_program_risk',
            'partner_id',
            'partner',
            'id',
            'RESTRICT'
        );

        $this->createIndex(
            'idx-travel_program_risk-program_id',
            'travel_program_risk',
            'program_id'
        );

        $this->addForeignKey(
            'fk-travel_program_risk-program_id',
            'travel_program_risk',
            'program_id',
            'travel_program',
            'id',
            'RESTRICT'
        );

        $this->createIndex(
            'idx-travel_program_risk-risk_id',
            'travel_program_risk',
            'risk_id'
        );

        $this->addForeignKey(
            'fk-travel_program_risk-risk_id',
            'travel_program_risk',
            'risk_id',
            'travel_risk',
            'id',
            'RESTRICT'
        );

        $this->createTable('{{%travel_program_period}}', [
            'id' => $this->primaryKey(),
            'partner_id' => $this->integer()->notNull(),
            'program_id' => $this->integer()->notNull(),
            'from_day' => $this->integer()->notNull(),
            'to_day' => $this->integer()->notNull(),
            'amount' => $this->float()->notNull(),
        ], null);
        
        $this->createIndex(
            'idx-travel_program_period-partner_id',
            'travel_program_period',
            'partner_id'
        );

        $this->addForeignKey(
            'fk-travel_program_period-partner_id',
            'travel_program_period',
            'partner_id',
            'partner',
            'id',
            'RESTRICT'
        );

        $this->createIndex(
            'idx-travel_program_period-program_id',
            'travel_program_period',
            'program_id'
        );

        $this->addForeignKey(
            'fk-travel_program_period-program_id',
            'travel_program_period',
            'program_id',
            'travel_program',
            'id',
            'RESTRICT'
        );

        $this->createTable('{{%travel_age_group}}', [
            'id' => $this->primaryKey(),
            'partner_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'from_age' => $this->integer()->notNull(),
            'to_age' => $this->integer()->notNull(),
            'coeff' => $this->float()->notNull(),
        ], null);
        
        $this->createIndex(
            'idx-travel_age_group-partner_id',
            'travel_age_group',
            'partner_id'
        );

        $this->addForeignKey(
            'fk-travel_age_group-partner_id',
            'travel_age_group',
            'partner_id',
            'partner',
            'id',
            'RESTRICT'
        );

        $this->createTable('{{%travel_extra_insurance}}', [
            'id' => $this->primaryKey(),
            'partner_id' => $this->integer()->notNull(),
            'name_ru' => $this->string()->notNull(),
            'name_uz' => $this->string()->notNull(),
            'name_en' => $this->string()->notNull(),
            'coeff' => $this->float()->notNull()->defaultValue(1),
            'status' => $this->boolean()->notNull(),
        ], null);

        $this->createIndex(
            'idx-travel_extra_insurance-partner_id',
            'travel_extra_insurance',
            'partner_id'
        );

        $this->addForeignKey(
            'fk-travel_extra_insurance-partner_id',
            'travel_extra_insurance',
            'partner_id',
            'partner',
            'id',
            'RESTRICT'
        );

        $this->createTable('{{%travel}}', [
            'id' => $this->primaryKey(),
            'partner_id' => $this->integer()->notNull(),
            'program_id' => $this->integer()->notNull(),
            'begin_date' => $this->date()->notNull(),
            'end_date' => $this->date()->notNull(),
            'days' => $this->integer()->notNull(),
            'purpose_id' => $this->integer()->notNull(),
            'group_type_id' => $this->integer()->notNull(),
            'amount_uzs' => $this->float()->notNull(),
            'amount_usd' => $this->float()->notNull(),
            'insurer_name' => $this->string()->notNull(),
            'insurer_address' => $this->string()->notNull(),
            'insurer_phone' => $this->string()->notNull(),
            'insurer_passport_series' => $this->string()->notNull(),
            'insurer_passport_number' => $this->string()->notNull(),
            'insurer_pinfl' => $this->string()->notNull(),
            'status' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ], null);

        $this->createIndex(
            'idx-travel-partner_id',
            'travel',
            'partner_id'
        );

        $this->addForeignKey(
            'fk-travel-partner_id',
            'travel',
            'partner_id',
            'partner',
            'id',
            'RESTRICT'
        );

        $this->createIndex(
            'idx-travel-program_id',
            'travel',
            'program_id'
        );

        $this->addForeignKey(
            'fk-travel-program_id',
            'travel',
            'program_id',
            'travel_program',
            'id',
            'RESTRICT'
        );

        $this->createIndex(
            'idx-travel-purpose_id',
            'travel',
            'purpose_id'
        );

        $this->addForeignKey(
            'fk-travel-purpose_id',
            'travel',
            'purpose_id',
            'travel_purpose',
            'id',
            'RESTRICT'
        );

        $this->createIndex(
            'idx-travel-group_type_id',
            'travel',
            'group_type_id'
        );

        $this->addForeignKey(
            'fk-travel-group_type_id',
            'travel',
            'group_type_id',
            'travel_group_type',
            'id',
            'RESTRICT'
        );

        $this->createTable('{{%travel_country}}', [
            'id' => $this->primaryKey(),
            'travel_id' => $this->integer()->notNull(),
            'country_id' => $this->integer()->notNull(),
        ], null);

        $this->createIndex(
            'idx-travel_country-travel_id',
            'travel_country',
            'travel_id'
        );

        $this->addForeignKey(
            'fk-travel_country-travel_id',
            'travel_country',
            'travel_id',
            'travel',
            'id',
            'RESTRICT'
        );

        $this->createIndex(
            'idx-travel_country-country_id',
            'travel_country',
            'country_id'
        );

        $this->addForeignKey(
            'fk-travel_country-country_id',
            'travel_country',
            'country_id',
            'country',
            'id',
            'RESTRICT'
        );

        $this->createTable('{{%travel_extra_insurance_bind}}', [
            'id' => $this->primaryKey(),
            'travel_id' => $this->integer()->notNull(),
            'extra_insurance_id' => $this->integer()->notNull(),
        ], null);

        $this->createIndex(
            'idx-travel_extra_insurance_bind-travel_id',
            'travel_extra_insurance_bind',
            'travel_id'
        );

        $this->addForeignKey(
            'fk-travel_extra_insurance_bind-travel_id',
            'travel_extra_insurance_bind',
            'travel_id',
            'travel',
            'id',
            'RESTRICT'
        );

        $this->createIndex(
            'idx-travel_extra_insurance_bind-extra_insurance_id',
            'travel_extra_insurance_bind',
            'extra_insurance_id'
        );

        $this->addForeignKey(
            'fk-travel_extra_insurance_bind-extra_insurance_id',
            'travel_extra_insurance_bind',
            'extra_insurance_id',
            'travel_extra_insurance',
            'id',
            'RESTRICT'
        );

        $this->createTable('{{%traveler}}', [
            'id' => $this->primaryKey(),
            'travel_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'birthday' => $this->date()->notNull(),
            'passport_series' => $this->string()->notNull(),
            'passport_number' => $this->string()->notNull(),
            'phone' => $this->string()->notNull(),
            'address' => $this->string()->notNull()
        ], null);

        $this->createIndex(
            'idx-traveler-travel_id',
            'traveler',
            'travel_id'
        );

        $this->addForeignKey(
            'fk-traveler-travel_id',
            'traveler',
            'travel_id',
            'travel',
            'id',
            'RESTRICT'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200905_114054_travel_refs cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200905_114054_travel_refs cannot be reverted.\n";

        return false;
    }
    */
}
