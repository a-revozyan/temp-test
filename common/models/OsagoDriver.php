<?php

namespace common\models;

use common\helpers\DateHelper;
use common\helpers\GeneralHelper;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "osago_driver".
 *
 * @property int $id
 * @property int $osago_id
 * @property string $name
 * @property string|null $pinfl
 * @property string|null $passport_series
 * @property string|null $passport_number
 * @property string $license_series
 * @property string $license_number
 * @property string $license_file
 * @property int|null $relationship_id
 * @property int|null $birthday
 * @property boolean|null $with_accident
 * @property string|null $license_given_date
 * @property integer|null $status
 * @property string|null $last_name
 * @property string|null $first_name
 * @property string|null $middle_name
 * @property string|null $created_at
 *
 * @property Osago $osago
 * @property Relationship $relationship
 */
class OsagoDriver extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public $licenseFile;

    public static function tableName()
    {
        return 'osago_driver';
    }

    public const STATUS = [
        'created' => 0,
        'verified' => 1,
    ];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['license_series','license_number', 'name'], 'required', 'when' => function($model) {
                return $model->name;
            }, 'whenClient' => "function (attribute, value) {
                return $('#osago-number_drivers_id').val() == 2;
            }", 'message' => Yii::t('app', 'Необходимо заполнить')],
            [['osago_id', 'relationship_id'], 'default', 'value' => null],
            [['osago_id', 'relationship_id', 'birthday', 'status'], 'integer'],
            [['with_accident'], 'boolean'],
            [['licenseFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, pdf'],
            [['name', 'pinfl', 'passport_series', 'passport_number', 'license_series', 'license_number', 'license_file', 'first_name', 'last_name', 'middle_name'], 'string', 'max' => 255],
            [['osago_id'], 'exist', 'skipOnError' => true, 'targetClass' => Osago::className(), 'targetAttribute' => ['osago_id' => 'id']],
            [['relationship_id'], 'exist', 'skipOnError' => true, 'targetClass' => Relationship::className(), 'targetAttribute' => ['relationship_id' => 'id']],
            [['license_given_date', 'created_at'], 'safe'],
        ];
    }

    public function fields()
    {
        $lang = GeneralHelper::lang_of_local();
        $fields = parent::fields();
        $fields['relationship'] = function ($model) use ($lang){
            if (is_null($model->relationship))
                return '';
            return $model->relationship->{'name_' . $lang};
        };
        $fields['birthday'] = function ($model){
            if (is_null($model->birthday))
                return null;
            return date('d.m.Y', $model->birthday);
        };

        return $fields;
    }

    public function uploadLicense()
    {
        if ($this->validate()) {
            $this->licenseFile->saveAs('uploads/license_files/osago/' . $this->license_file);
            return true;
        } else {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'osago_id' => Yii::t('app', 'Osago ID'),
            'name' => Yii::t('app', 'Name'),
            'pinfl' => Yii::t('app', 'Pinfl'),
            'passport_series' => Yii::t('app', 'Passport Series'),
            'passport_number' => Yii::t('app', 'Passport Number'),
            'license_series' => Yii::t('app', 'License Series'),
            'license_number' => Yii::t('app', 'License Number'),
            'relationship_id' => Yii::t('app', 'Relationship ID'),
        ];
    }

    /**
     * Gets query for [[Osago]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOsago()
    {
        return $this->hasOne(Osago::className(), ['id' => 'osago_id']);
    }

    /**
     * Gets query for [[Relationship]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRelationship()
    {
        return $this->hasOne(Relationship::className(), ['id' => 'relationship_id']);
    }

    public function getShortArr()
    {
        return [
            'id' => $this->id,
            'passport_series' => $this->passport_series,
            'passport_number' => $this->passport_number,
            'license_series' => $this->license_series,
            'license_number' => $this->license_number,
            'license_given_date' => empty($this->license_given_date) ? null : DateHelper::date_format($this->license_given_date, 'Y-m-d', 'd.m.Y'),
            'relationship_id' => $this->relationship_id, //busiz frontendda realizatsiya qilolmaganlari uchun qo'shib qo'ydim
            'relationship' => !is_null($this->relationship) ? $this->relationship->getForIdNameArr() : null,
            'birthday' => is_null($this->birthday) ? null : date("d.m.Y", $this->birthday),
            'with_accident' =>  $this->with_accident,
            'pinfl' =>  $this->pinfl,
        ];
    }

    public static function getShortArrCollection($drivers)
    {
        $_driver = [];
        foreach ($drivers as $driver) {
            $_driver[] = $driver->getShortArr();
        }
        return $_driver;
    }
}
