<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "car_price_request".
 *
 * @property int $id
 * @property int|null $brand_id
 * @property int|null $model_id
 * @property int|null $transmission_type
 * @property int|null $fuel_type
 * @property int|null $year
 * @property int|null $mileage
 * @property float|null $engine_capacity
 * @property int|null $average_price
 * @property int|null $among_cars_count
 * @property int|null $partner_id
 * @property string|null $created_at
 * @property integer|null $fuser_id
 */
class CarPriceRequest extends \yii\db\ActiveRecord
{

    public const FUEL_TYPE = [
        'Benzin' => 0,
        'Gaz' => 1,
    ];

    public const TRANSMISSION_TYPE = [
        'Mexanika' => 0,
        'Avtomat' => 1,
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'car_price_request';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['brand_id', 'model_id', 'transmission_type', 'fuel_type', 'year', 'mileage', 'average_price', 'among_cars_count', 'partner_id', 'fuser_id'], 'default', 'value' => null],
            [['brand_id', 'model_id', 'transmission_type', 'fuel_type', 'year', 'mileage', 'average_price', 'among_cars_count', 'partner_id', 'fuser_id'], 'integer'],
            [['engine_capacity'], 'number'],
            [['created_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'brand_id' => 'Brand',
            'model_id' => 'Model',
            'transmission_type' => 'Transmission Type',
            'fuel_type' => 'Fuel Type',
            'year' => 'Year',
            'mileage' => 'Mileage',
            'engine_capacity' => 'Engine Capacity',
            'average_price' => 'Average Price',
            'among_cars_count' => 'Among Cars Count',
            'partner_id' => 'Partner ID',
            'created_at' => 'Created At',
            'fuser_id' => 'Fuser ID',
        ];
    }
}
