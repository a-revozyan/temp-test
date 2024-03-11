<?php

use yii\db\Migration;

/**
 * Class m220511_151406_add_bridge_company_id_to_kasko
 */
class m220511_151406_add_bridge_company_id_to_kasko extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('kasko', 'bridge_company_id', $this->integer());

        $this->addForeignKey(
            'fk-kasko-bridge_company_id',
            'kasko',
            'bridge_company_id',
            'bridge_company',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220511_151406_add_bridge_company_id_to_kasko cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220511_151406_add_bridge_company_id_to_kasko cannot be reverted.\n";

        return false;
    }
    */
}
