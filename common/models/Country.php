<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "country".
 *
 * @property int $id
 * @property string $name_ru
 * @property string $name_uz
 * @property string $name_en
 * @property bool $schengen
 * @property int|null $parent_id
 * @property string|null $code
 * @property string|null $image
 * @property float|null $order
 * @property integer|null $kapital_id
 *
 * @property Country $parent
 * @property Country[] $countries
 * @property TravelCountry[] $travelCountries
 * @property TravelProgramCountry[] $travelProgramCountries
 */
class Country extends \yii\db\ActiveRecord
{
    public $imageFile;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'country';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order'], 'double'],
            [['name_ru', 'name_uz', 'name_en', 'schengen'], 'required'],
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png', 'on' => 'insert'],
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png', 'on' => 'update'],
            [['schengen'], 'boolean'],
            [['parent_id'], 'default', 'value' => null],
            [['parent_id'], 'integer'],
            [['name_ru', 'name_uz', 'name_en', 'code', 'image'], 'string', 'max' => 255],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Country::className(), 'targetAttribute' => ['parent_id' => 'id']],
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
            'schengen' => Yii::t('app', 'Schengen'),
            'parent_id' => Yii::t('app', 'Parent ID'),
            'code' => Yii::t('app', 'Code'),
            'image' => Yii::t('app', 'Image'),
            'order' => Yii::t('app', 'Order'),
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $this->imageFile->saveAs('../../frontend/web/uploads/countries/' . $this->image);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Gets query for [[Parent]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Country::className(), ['id' => 'parent_id']);
    }

    /**
     * Gets query for [[Countries]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCountries()
    {
        return $this->hasMany(Country::className(), ['parent_id' => 'id']);
    }

    /**
     * Gets query for [[TravelCountries]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTravelCountries()
    {
        return $this->hasMany(TravelCountry::className(), ['country_id' => 'id']);
    }

    /**
     * Gets query for [[TravelProgramCountries]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTravelProgramCountries()
    {
        return $this->hasMany(TravelProgramCountry::className(), ['country_id' => 'id']);
    }

    public static function getShortArrCollection($models)
    {
        $_models = [];
        foreach ($models as $model) {
            $_models[] = $model->getShortArr();
        }

        return $_models;
    }
    public static function getShortArrForAdminCollection($models)
    {
        $_models = [];
        foreach ($models as $model) {
            $_models[] = $model->getShortForAdminArr();
        }

        return $_models;
    }
    public function getShortArr()
    {
        return [
            'code' => $this->code,
        ];
    }
    public function getShortForAdminArr()
    {
        return [
            'code' => $this->code,
            'name_ru' => $this->name_ru,
        ];
    }
}
