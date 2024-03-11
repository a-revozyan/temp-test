<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "travel_family_koef".
 *
 * @property int $id
 * @property int|null $partner_id
 * @property int|null $members_count
 * @property float|null $koef
 *
 * @property Partner $partner
 */
class TravelFamilyKoef extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'travel_family_koef';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['partner_id', 'members_count'], 'default', 'value' => null],
            [['partner_id', 'members_count'], 'integer'],
            [['koef'], 'number'],
            [['partner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Partner::className(), 'targetAttribute' => ['partner_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'partner_id' => 'Partner ID',
            'members_count' => 'Members Count',
            'koef' => 'Koef',
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
