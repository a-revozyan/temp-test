<?php

use yii\db\Migration;

/**
 * Class m200828_071334_osago_ref_coeff_alter
 */
class m200828_071334_osago_ref_coeff_alter extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('autotype', 'coeff', $this->float()->notNull());
        $this->alterColumn('period', 'coeff', $this->float()->notNull());
        $this->alterColumn('region', 'coeff', $this->float()->notNull());
        $this->alterColumn('citizenship', 'coeff', $this->float()->notNull());
        $this->alterColumn('number_drivers', 'coeff', $this->float()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200828_071334_osago_ref_coeff_alter cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200828_071334_osago_ref_coeff_alter cannot be reverted.\n";

        return false;
    }
    */
}
