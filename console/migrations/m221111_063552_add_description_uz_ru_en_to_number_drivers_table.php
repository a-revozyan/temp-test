<?php

use yii\db\Migration;

/**
 * Class m221111_063552_add_description_uz_ru_en_to_number_drivers_table
 */
class m221111_063552_add_description_uz_ru_en_to_number_drivers_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('number_drivers', 'description_uz', $this->string());
        $this->addColumn('number_drivers', 'description_ru', $this->string());
        $this->addColumn('number_drivers', 'description_en', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221111_063552_add_description_uz_ru_en_to_number_drivers_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221111_063552_add_description_uz_ru_en_to_number_drivers_table cannot be reverted.\n";

        return false;
    }
    */
}
