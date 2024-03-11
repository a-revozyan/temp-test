<?php

use yii\db\Migration;

/**
 * Class m210413_183536_promo
 */
class m210413_183536_promo extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%promo}}', [
            'id' => $this->primaryKey(),
            'code' => $this->string()->notNull(),
            'percent' => $this->float()->notNull(),
        ], null);

        $this->addColumn('travel', 'promo_id', $this->integer());
        $this->addColumn('travel', 'promo_percent', $this->float());
        $this->addColumn('travel', 'promo_amount', $this->float());

        $this->addColumn('osago', 'promo_id', $this->integer());
        $this->addColumn('osago', 'promo_percent', $this->float());
        $this->addColumn('osago', 'promo_amount', $this->float());

        $this->addColumn('kasko', 'promo_id', $this->integer());
        $this->addColumn('kasko', 'promo_percent', $this->float());
        $this->addColumn('kasko', 'promo_amount', $this->float());

        $this->addColumn('accident', 'promo_id', $this->integer());
        $this->addColumn('accident', 'promo_percent', $this->float());
        $this->addColumn('accident', 'promo_amount', $this->float());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210413_183536_promo cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210413_183536_promo cannot be reverted.\n";

        return false;
    }
    */
}
