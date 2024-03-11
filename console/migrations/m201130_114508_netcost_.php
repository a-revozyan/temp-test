<?php

use yii\db\Migration;

/**
 * Class m201130_114508_netcost_
 */
class m201130_114508_netcost_ extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('accident', 'insurer_passport_file', $this->string());
        $this->alterColumn('accident_insurer', 'passport_file', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201130_114508_netcost_ cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201130_114508_netcost_ cannot be reverted.\n";

        return false;
    }
    */
}
