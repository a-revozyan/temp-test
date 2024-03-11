<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "click_request".
 *
 * @property int $id
 * @property string|null $url
 * @property string|null $request_body
 * @property string|null $response_body
 * @property string|null $send_date
 * @property int|null $model_id
 * @property string|null $model_class
 */
class ClickRequest extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'click_request';
    }

    public static function create($url, $request_body, $response_array, $model_id, $model_class)
    {
        $click_request = new ClickRequest();
        $click_request->url = $url;
        $click_request->request_body = json_encode($request_body);
        $click_request->response_body = is_array($response_array) ? json_encode($response_array) : $response_array;
        $click_request->send_date = date('Y-m-d H:i:s');
        $click_request->model_id = $model_id;
        $click_request->model_class = $model_class;
        $click_request->save();

        return $click_request;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['request_body', 'response_body'], 'string'],
            [['send_date'], 'safe'],
            [['model_id'], 'default', 'value' => null],
            [['model_id'], 'integer'],
            [['url', 'model_class'], 'string', 'max' => 255],
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
            'send_date' => 'Send Date',
            'model_id' => 'Model ID',
            'model_class' => 'Model Class',
        ];
    }
}
