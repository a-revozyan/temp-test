<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "warehouse".
 *
 * @property int $id
 * @property int $partner_id
 * @property int $product_id
 * @property string $series
 * @property string $number
 * @property integer $status
 *
 * @property Partner $partner
 * @property Product $product
 */
class Warehouse extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'warehouse';
    }

    public const STATUS = [
        'new' => 0,
        'reserve' => 1,
        'paid' => 2,
        'cancel' => 3,
    ];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['partner_id', 'product_id'], 'default', 'value' => null],
            [['partner_id', 'product_id'], 'integer'],
            [['series', 'number','status'], 'string', 'max' => 255],
            [['partner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Partner::className(), 'targetAttribute' => ['partner_id' => 'id']],
//            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'partner_id' => Yii::t('app', 'Partner ID'),
            'product_id' => Yii::t('app', 'Product ID'),
            'series' => Yii::t('app', 'Series'),
            'number' => Yii::t('app', 'Number'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * Gets query for [[Partner]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPartner()
    {
        return $this->hasOne(Partner::className(), ['id' => 'partner_id']);
    }

    /**
     * Gets query for [[Product]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    public static function getFullArrCollection($models)
    {
        $_models = [];
        foreach ($models as $model) {
            $_models[] = $model->getFullArr();
        }
        return $_models;
    }

    public function getShortArr()
    {
        return [
            'id' => $this->id,
            'series' => $this->series,
            'number' => $this->number,
        ];
    }

    public function getFullArr()
    {
        $partner = $this->partner;
        return [
            'id' => $this->id,
            'series' => $this->series,
            'number' => $this->number,
            'partner' => empty($this->partner) ? null : $partner->getForIdNameArr(),
            'status' => $this->status,
        ];
    }
}
