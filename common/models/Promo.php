<?php

namespace common\models;

use common\helpers\DateHelper;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "promo".
 *
 * @property int $id
 * @property string $code
 * @property float $amount
 * @property integer $status
 * @property integer $amount_type
 * @property integer $begin_date
 * @property integer $end_date
 * @property integer $number
 * @property integer $type
 */
class Promo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'promo';
    }

    public const AMOUNT_TYPE = [
        'percent' => 0,
        'fixed' => 1
    ];

    public const STATUS = [
        'inactive' => 0,
        'active' => 1
    ];

    public const TYPE = [
        'simple' => 0,
        'unique_link' => 1
    ];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'amount', 'amount_type', 'status', 'number'], 'required'],
            [['begin_date', 'end_date', 'begin_date', 'end_date'], 'safe'],
            [['amount_type', 'status', 'type'], 'integer'],
            [['amount_type', 'status'], 'in', 'range' => [0,1]],
            [['amount'], 'number', 'max' => 0],
            [['code'], 'string', 'max' => 255],
            ['number', 'integer', 'min' => 0]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'code' => Yii::t('app', 'Code'),
            'amount_type' => Yii::t('app', 'Type of amount'),
            'amount' => Yii::t('app', 'Amount'),
            'status' => Yii::t('app', 'Status'),
            'begin_date' => Yii::t('app', 'Begin date'),
            'end_date' => Yii::t('app', 'End date'),
            'number' => Yii::t('app', 'Number'),
        ];
    }


    public static function getFullArrCollection($models)
    {
        $_models = [];
        foreach ($models as $model) {
            $_models[] = $model->getFullArr();
        }

        return $_models;
    }

    public function getProducts()
    {
        return $this->hasMany(Product::className(), ['id' => 'product_id'])->viaTable('product_promo', ['promo_id' => 'id']);
    }

    public function getFullArr()
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'amount' => $this->amount,
            'begin_date' => is_null($this->begin_date) ? null : date('d.m.Y', strtotime($this->begin_date)),
            'end_date' => is_null($this->end_date) ? null : date('d.m.Y', strtotime($this->end_date)),
            'amount_type' => $this->amount_type,
            'status' => $this->status,
            'number' => $this->number,
            'products' => array_values(Product::getIdNameCollection($this->products)),
        ];
    }
}
