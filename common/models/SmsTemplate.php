<?php

namespace common\models;

use common\helpers\GeneralHelper;
use common\services\TelegramService;
use Yii;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "sms_template".
 *
 * @property int $id
 * @property string|null $text
 * @property string|null $method
 * @property string|null $file_url
 * @property string|null $region_car_numbers
 * @property int|null $number_drivers_id
 * @property string|null $registered_from_date
 * @property string|null $registered_till_date
 * @property string|null $bought_from_date
 * @property string|null $bought_till_date
 * @property int|null $type
 * @property int|null $all_users_count
 * @property int|null $status
 * @property string|null $begin_date
 * @property string|null $end_date
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property integer|null $product
 */
class SmsTemplate extends \yii\db\ActiveRecord
{
    public const STATUS = [
        'created' => 0,
        'started' => 1,
        'paused' => 2,
        'ended' => 3,
        'archived' => 4,
    ];

    public const TYPE = [
        'first_telegram_else_sms' => 1,
        'users_which_have_telegram_via_telegram' => 2,
        'users_which_have_not_telegram_via_sms' => 3,
        'all_users_via_sms' => 4,
    ];

    public const METHOD = TelegramService::METHOD;

    public function attributes(){
        return [...parent::attributes(), 'sms_count'];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sms_template';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['text'], 'string'],
            [['method', 'number_drivers_id', 'type', 'all_users_count', 'status'], 'default', 'value' => null],
            [['number_drivers_id', 'type', 'all_users_count', 'status', 'product'], 'integer'],
            [['registered_from_date', 'registered_till_date', 'bought_from_date', 'bought_till_date', 'begin_date', 'end_date'], 'safe'],
            [['file_url', 'region_car_numbers', 'method'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'text' => 'Text',
            'method' => 'Method',
            'file_url' => 'File Url',
            'region_car_numbers' => 'Region Car Numbers',
            'number_drivers_id' => 'Number Drivers ID',
            'registered_from_date' => 'Registered From Date',
            'registered_till_date' => 'Registered Till Date',
            'bought_from_date' => 'Bought From Date',
            'bought_till_date' => 'Bought Till Date',
            'type' => 'Type',
            'all_users_count' => 'All Users Count',
            'status' => 'Status',
            'begin_date' => 'Begin Date',
            'end_date' => 'End Date',
            'product' => 'Product',
        ];
    }

    public function getNumberDrivers()
    {
        return $this->hasOne(NumberDrivers::className(), ['id' => 'number_drivers_id']);
    }

    public function getSmsActiveHistories()
    {
        return $this->hasMany(SmsHistory::className(), ['sms_template_id' => 'id'])->where(['not', ['status' => SmsHistory::STATUS['created']]]);
    }

    public function getSmsCount()
    {
        return $this->getSmsActiveHistories()->count();
    }

    public function getFileUrl()
    {
        if (empty($this->file_url))
            return null;
        return 'http://back-api.sugurtabozor.uz/uploads/sms_templates/' . $this->file_url;
//        return (GeneralHelper::env('backapi_project_website') . '/uploads/sms_templates/' . $this->file_url);
    }

    public static function getShortArrCollection($models)
    {
        $_models = [];
        foreach ($models as $model) {
            $_models[] = $model->getShortArr();
        }

        return $_models;
    }

    public function getShortArr()
    {
        return [
            'id' => $this->id,
            'text' => substr($this->text, 0, 255),
            'method' => $this->method,
            'file_url' => $this->getFileUrl(),
            'type' => $this->type,
            'begin_date' => $this->begin_date,
            'end_date' => $this->end_date,
            'created_at' => $this->created_at,
            'all_users_count' => $this->all_users_count,
            'sms_count' => $this->sms_count,
            'product' => $this->product,
            'status' => $this->status,
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

    public function getFullArr()
    {
        return [
            'id' => $this->id,
            'text' => $this->text,
            'method' => $this->method,
            'file_url' => $this->getFileUrl(),
            'region_car_numbers' => !empty($this->region_car_numbers) ? explode(',', $this->region_car_numbers) : [],
            'number_drivers' => !is_null($this->numberDrivers) ? $this->numberDrivers->getShortInRuArr() : null,
            'registered_from_date' => $this->registered_from_date,
            'registered_till_date' => $this->registered_till_date,
            'bought_from_date' => $this->bought_from_date,
            'bought_till_date' => $this->bought_till_date,
            'type' => $this->type,
            'begin_date' => $this->begin_date,
            'end_date' => $this->end_date,
            'created_at' => $this->created_at,
            'all_users_count' => $this->all_users_count,
            'sms_count' => $this->smsCount,
            'product' => $this->product,
            'status' => $this->status,
        ];
    }
}
