<?php

use yii\db\Migration;

/**
 * Class m220621_132344_add_warehouse_id_to_travel
 */
class m220621_132344_add_warehouse_id_to_travel extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('travel', 'warehouse_id', $this->integer());

        $this->addForeignKey(
            'fk-travel-warehouse_id',
            'travel',
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
        echo "m220621_132344_add_warehouse_id_to_travel cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220621_132344_add_warehouse_id_to_travel cannot be reverted.\n";

        return false;
    }
    */
}
