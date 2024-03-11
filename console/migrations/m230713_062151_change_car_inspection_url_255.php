<?php

use yii\db\Migration;

/**
 * Class m230713_062151_change_car_inspection_url_255
 */
class m230713_062151_change_car_inspection_url_255 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('car_inspection_file', 'url', $this->string(500));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230713_062151_change_car_inspection_url_255 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230713_062151_change_car_inspection_url_255 cannot be reverted.\n";

        return false;
    }
    */
}
