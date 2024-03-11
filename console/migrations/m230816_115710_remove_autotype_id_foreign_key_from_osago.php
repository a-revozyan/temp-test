<?php

use yii\db\Migration;

/**
 * Class m230816_115710_remove_autotype_id_foreign_key_from_osago
 */
class m230816_115710_remove_autotype_id_foreign_key_from_osago extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk-osago-autotype_id', 'osago');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230816_115710_remove_autotype_id_foreign_key_from_osago cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230816_115710_remove_autotype_id_foreign_key_from_osago cannot be reverted.\n";

        return false;
    }
    */
}
