<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "car_inspection_partner_request".
 *
 * @property int $id
 * @property string|null $url
 * @property string|null $request_body
 * @property string|null $response_body
 * @property int|null $partner_id
 * @property int|null $taken_time
 * @property string|null $send_date
 */
class CarInspectionPartnerRequest extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'car_inspection_partner_request';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['partner_id', 'taken_time'], 'default', 'value' => null],
            [['partner_id', 'taken_time'], 'integer'],
            [['send_date'], 'safe'],
            [['url', 'request_body'], 'string', 'max' => 255],
            [['response_body'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'url' => 'Url',
            'request_body' => 'Request Body',
            'response_body' => 'Response Body',
            'partner_id' => 'Partner ID',
            'taken_time' => 'Taken Time',
            'send_date' => 'Send Date',
        ];
    }
}
