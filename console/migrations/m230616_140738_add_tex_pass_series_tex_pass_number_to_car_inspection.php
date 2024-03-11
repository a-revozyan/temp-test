<?php

use yii\db\Migration;

/**
 * Class m230616_140738_add_tex_pass_series_tex_pass_number_to_car_inspection
 */
class m230616_140738_add_tex_pass_series_tex_pass_number_to_car_inspection extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('car_inspection', 'tex_pass_series', $this->string());
        $this->addColumn('car_inspection', 'tex_pass_number', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230616_140738_add_tex_pass_series_tex_pass_number_to_car_inspection cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230616_140738_add_tex_pass_series_tex_pass_number_to_car_inspection cannot be reverted.\n";

        return false;
    }
    */
}
