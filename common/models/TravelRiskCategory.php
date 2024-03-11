<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "travel_risk_category".
 *
 * @property int $id
 * @property string|null $name
 *
 * @property TravelRisk[] $travelRisks
 */
class TravelRiskCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'travel_risk_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    /**
     * Gets query for [[TravelRisks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTravelRisks()
    {
        return $this->hasMany(TravelRisk::className(), ['category_id' => 'id']);
    }
}
