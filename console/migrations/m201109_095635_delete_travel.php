<?php

use yii\db\Migration;

/**
 * Class m201109_095635_delete_travel
 */
class m201109_095635_delete_travel extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        // $this->dropIndex(
        //     'idx-osago_driver-osago_id',
        //     'osago_driver'
        // );

        // $this->dropForeignKey(
        //     'fk-osago_driver-osago_id',
        //     'osago_driver'
        // );

        // $this->createIndex(
        //     'idx-osago_driver-osago_id',
        //     'osago_driver',
        //     'osago_id'
        // );

        // $this->addForeignKey(
        //     'fk-osago_driver-osago_id',
        //     'osago_driver',
        //     'osago_id',
        //     'osago',
        //     'id',
        //     'CASCADE'
        // );

        $this->dropIndex(
            'idx-travel_country-travel_id',
            'travel_country'
        );

        $this->dropForeignKey(
            'fk-travel_country-travel_id',
            'travel_country'
        );

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
            'CASCADE'
        );


        $this->dropIndex(
            'idx-travel_extra_insurance_bind-travel_id',
            'travel_extra_insurance_bind'
        );

        $this->dropForeignKey(
            'fk-travel_extra_insurance_bind-travel_id',
            'travel_extra_insurance_bind'
        );

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
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201109_095635_delete_travel cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201109_095635_delete_travel cannot be reverted.\n";

        return false;
    }
    */
}
