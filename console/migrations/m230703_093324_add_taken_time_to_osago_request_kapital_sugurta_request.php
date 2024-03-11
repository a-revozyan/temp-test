<?php

use yii\db\Migration;

/**
 * Class m230703_093324_add_taken_time_to_osago_request_kapital_sugurta_request
 */
class m230703_093324_add_taken_time_to_osago_request_kapital_sugurta_request extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('osago_requestes', 'taken_time', $this->integer());
        $this->addColumn('kapital_sugurta_request', 'taken_time', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230703_093324_add_taken_time_to_osago_request_kapital_sugurta_request cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230703_093324_add_taken_time_to_osago_request_kapital_sugurta_request cannot be reverted.\n";

        return false;
    }
    */
}
