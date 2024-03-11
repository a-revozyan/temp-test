<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "accident_insurer".
 *
 * @property int $id
 * @property int $accident_id
 * @property string $name
 * @property string $birthday
 * @property string $passport_file
 * @property string $passport_series
 * @property string $passport_number
 * @property string $identity_number
 * @property integer $osago_driver_id
 *
 * @property Accident $accident
 */
class AccidentInsurer extends \yii\db\ActiveRecord
{
    public $passFile;
    public $isInsurer;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'accident_insurer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['accident_id', 'birthday'], 'required', 'message' => Yii::t('app', 'Необходимо заполнить')],
            [['passport_series','passport_number'], 'required', 'when' => function($model) {
                return !$model->identity_number;
            }, 'whenClient' => "function (attribute, value) {
                return !$('#accidentinsurer-' + attribute['name'].substr(1, 1) + '-identity_number').val();
            }", 'message' => Yii::t('app', 'Необходимо заполнить')],
            ['identity_number', 'required', 'when' => function($model) {
                return !$model->passport_series || !$model->passport_number;
            }, 'whenClient' => "function (attribute, value) {
                let order = attribute['name'].substr(1, 1);
                return !$('#accidentinsurer-' + order + '-passport_series').val() || !$('#accidentinsurer-' + order + '-passport_number').val();
            }", 'message' => Yii::t('app', 'Необходимо заполнить')],
            [['accident_id'], 'integer'],
            [['birthday', 'name'], 'safe'],
            [['name', 'passport_file', 'passport_series', 'passport_number', 'identity_number'], 'string', 'max' => 255],
            [['passFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, pdf, JPEG'],
            [['accident_id'], 'exist', 'skipOnError' => true, 'targetClass' => Accident::className(), 'targetAttribute' => ['accident_id' => 'id']],
        ];
    }

    public function uploadPass()
    {
        if ($this->validate()) {
            $this->passFile->saveAs('uploads/passport_files/accident/' . $this->passport_file);
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
            'id' => 'ID',
            'accident_id' => 'Accident ID',
            'name' => 'Name',
            'birthday' => 'Birthday',
            'passport_file' => 'Passport File',
        ];
    }

    /**
     * Gets query for [[Accident]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAccident()
    {
        return $this->hasOne(Accident::className(), ['id' => 'accident_id']);
    }
}
