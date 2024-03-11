<?php

use yii\db\Migration;

/**
 * Class m220424_181516_add_warehouse_id_to_kasko_table
 */
class m220424_181516_add_warehouse_id_to_kasko_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('kasko', 'warehouse_id', $this->integer());

        $this->addForeignKey(
            'fk-kasko-warehouse_id',
            'kasko',
            'warehouse_id',
            'warehouse',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220424_181516_add_warehouse_id_to_kasko_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220424_181516_add_warehouse_id_to_kasko_table cannot be reverted.\n";

        return false;
    }
    */
}
