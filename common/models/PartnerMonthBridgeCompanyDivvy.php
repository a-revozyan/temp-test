<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "partner_month_bridge_company_divvy".
 *
 * @property int $id
 * @property int|null $bridge_company_id
 * @property int|null $partner_id
 * @property int|null $product_id
 * @property int|null $number_drivers_id
 * @property string|null $month
 * @property float|null $percent
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class PartnerMonthBridgeCompanyDivvy extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'partner_month_bridge_company_divvy';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bridge_company_id', 'partner_id', 'product_id', 'number_drivers_id'], 'default', 'value' => null],
            [['bridge_company_id', 'partner_id', 'product_id', 'number_drivers_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['percent'], 'double'],
            [['month'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bridge_company_id' => 'Bridge Company ID',
            'partner_id' => 'Partner ID',
            'product_id' => 'Product ID',
            'number_drivers_id' => 'Number Drivers ID',
            'month' => 'Month',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getPartner()
    {
        return $this->hasOne(Partner::className(), ['id' => 'partner_id']);
    }

    public function getBridgeCompany()
    {
        return $this->hasOne(BridgeCompany::className(), ['id' => 'bridge_company_id']);
    }

    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    public function getNumberDrivers()
    {
        return $this->hasOne(NumberDrivers::className(), ['id' => 'number_drivers_id']);
    }

    public static function getShortArrCollection($models)
    {
        $_models = [];
        foreach ($models as $model) {
            $_models[] = $model->getShortArr();
        }

        return $_models;
    }

    public function getShortArr()
    {
        $partner = $this->partner;
        $bridge_company = $this->bridgeCompany;
        $product = $this->product;
        $number_drivers = $this->numberDrivers;
        return [
            'bridge_company' => !empty($bridge_company) ? $bridge_company->getIdNameArr() : null,
            'partner' => !empty($partner) ? $partner->getForIdNameArr() : null,
            'product' => !empty($product) ? $product->getIdNameArr() : null,
            'number_drivers' => !empty($number_drivers) ? $number_drivers->getShortInRuArr() : null,
            'month' => $this->month,
            'percent' => $this->percent,
        ];
    }
}
