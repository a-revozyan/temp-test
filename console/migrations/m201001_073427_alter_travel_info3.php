<?php

use yii\db\Migration;

/**
 * Class m201001_073427_alter_travel_info3
 */
class m201001_073427_alter_travel_info3 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->addColumn('{{%travel_partner_info}}', 'assistance_uz', $this->string());
        $this->addColumn('{{%travel_partner_info}}', 'franchise_uz', $this->text());
        $this->addColumn('{{%travel_partner_info}}', 'limitation_uz', $this->text());
        $this->addColumn('{{%travel_partner_info}}', 'rules_uz', $this->string());
        $this->addColumn('{{%travel_partner_info}}', 'policy_example_uz', $this->string());
        $this->addColumn('{{%travel_partner_info}}', 'assistance_en', $this->string());
        $this->addColumn('{{%travel_partner_info}}', 'franchise_en', $this->text());
        $this->addColumn('{{%travel_partner_info}}', 'limitation_en', $this->text());
        $this->addColumn('{{%travel_partner_info}}', 'rules_en', $this->string());
        $this->addColumn('{{%travel_partner_info}}', 'policy_example_en', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201001_073427_alter_travel_info3 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201001_073427_alter_travel_info3 cannot be reverted.\n";

        return false;
    }
    */
}
