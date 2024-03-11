<?php

use yii\db\Migration;

/**
 * Class m220516_145103_add_description_ru_uz_en_to_kasko_risk_table
 */
class m220516_145103_add_description_ru_uz_en_to_kasko_risk_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('kasko_risk', 'description', 'description_ru');
        $this->addColumn('kasko_risk', 'description_uz', $this->string());
        $this->addColumn('kasko_risk', 'description_en', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220516_145103_add_description_ru_uz_en_to_kasko_risk_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220516_145103_add_description_ru_uz_en_to_kasko_risk_table cannot be reverted.\n";

        return false;
    }
    */
}
