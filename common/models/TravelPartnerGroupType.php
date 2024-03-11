<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "travel_partner_group_type".
 *
 * @property int $id
 * @property int $partner_id
 * @property int $group_type_id
 * @property float $coeff
 *
 * @property Partner $partner
 * @property TravelGroupType $groupType
 */
class TravelPartnerGroupType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'travel_partner_group_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['partner_id', 'group_type_id'], 'required'],
            [['partner_id', 'group_type_id'], 'default', 'value' => null],
            [['partner_id', 'group_type_id'], 'integer'],
            [['coeff'], 'number'],
            [['partner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Partner::className(), 'targetAttribute' => ['partner_id' => 'id']],
            [['group_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => TravelGroupType::className(), 'targetAttribute' => ['group_type_id' => 'id']],
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
            'group_type_id' => Yii::t('app', 'Group Type ID'),
            'coeff' => Yii::t('app', 'Coeff'),
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
     * Gets query for [[GroupType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGroupType()
    {
        return $this->hasOne(TravelGroupType::className(), ['id' => 'group_type_id']);
    }
}
