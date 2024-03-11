<?php

use yii\db\Migration;

/**
 * Class m201103_052004_netkost_offert
 */
class m201103_052004_netkost_offert extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    	$this->addColumn('partner_product', 'public_offer_ru', $this->string());
		$this->addColumn('partner_product', 'public_offer_uz', $this->string());
		$this->addColumn('partner_product', 'public_offer_en', $this->string());

		$this->addColumn('partner_product', 'conditions_ru', $this->string());
		$this->addColumn('partner_product', 'conditions_uz', $this->string());
		$this->addColumn('partner_product', 'conditions_en', $this->string());

        $this->addColumn('partner_product', 'delivery_info_ru', $this->string());
        $this->addColumn('partner_product', 'delivery_info_uz', $this->string());
        $this->addColumn('partner_product', 'delivery_info_en', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201103_052004_netkost_offert cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201103_052004_netkost_offert cannot be reverted.\n";

        return false;
    }
    */
}
