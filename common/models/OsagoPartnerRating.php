<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "osago_partner_rating".
 *
 * @property int $id
 * @property int $partner_id
 * @property string $rating
 * @property int $order_no
 *
 * @property Partner $partner
 */
class OsagoPartnerRating extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'osago_partner_rating';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['partner_id', 'rating', 'order_no'], 'required'],
            [['partner_id', 'order_no'], 'default', 'value' => null],
            [['partner_id', 'order_no'], 'integer'],
            [['rating'], 'string', 'max' => 255],
            [['partner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Partner::className(), 'targetAttribute' => ['partner_id' => 'id']],
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
            'rating' => Yii::t('app', 'Rating'),
            'order_no' => Yii::t('app', 'Order No'),
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
}
