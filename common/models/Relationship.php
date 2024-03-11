<?php

namespace common\models;

use common\helpers\GeneralHelper;
use Yii;

/**
 * This is the model class for table "relationship".
 *
 * @property int $id
 * @property string $name_ru
 * @property string $name_uz
 * @property string $name_en
 *
 * @property OsagoDriver[] $osagoDrivers
 */
class Relationship extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'relationship';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name_ru', 'name_uz', 'name_en'], 'required'],
            [['name_ru', 'name_uz', 'name_en'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name_ru' => Yii::t('app', 'Name Ru'),
            'name_uz' => Yii::t('app', 'Name Uz'),
            'name_en' => Yii::t('app', 'Name En'),
            'name' => Yii::t('app', 'Name'),
        ];
    }

    public function attributes()
    {
        $attributes = parent::attributes();
        $attributes[] = 'name';
        return $attributes;
    }

    /**
     * Gets query for [[OsagoDrivers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOsagoDrivers()
    {
        return $this->hasMany(OsagoDriver::className(), ['relationship_id' => 'id']);
    }

    public function getForIdNameArr()
    {
        return [
            'id' => $this->id,
            'name' => $this->{'name_' . GeneralHelper::lang_of_local()},
        ];
    }
}
