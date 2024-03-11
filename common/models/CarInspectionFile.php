<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "car_inspection_file".
 *
 * @property int $id
 * @property int|null $car_inspection_id
 * @property string|null $url
 * @property int|null $type
 * @property int|null $status
 */
class CarInspectionFile extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'car_inspection_file';
    }

    public const TYPE = [
        'video' => 0
    ];

    public const STATUS = [
        'created' => 0,
        'uploaded' => 1,
    ];

    public const FILES_COUNT = 10;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['car_inspection_id', 'type', 'status'], 'default', 'value' => null],
            [['car_inspection_id', 'type', 'status'], 'integer'],
            [['url'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'car_inspection_id' => 'Car Inspection ID',
            'url' => 'Url',
            'type' => 'Type',
            'status' => 'Status',
        ];
    }

    public static function getShortArrCollection($models = [])
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
            'url' => self::urlWithoutSas($this->url),
            'type' => $this->type,
        ];
    }

    public function getShortArrForUpload()
    {
        return [
            'id' => $this->id,
            'url' => $this->url,
            'type' => $this->type,
        ];
    }

    public static function urlWithoutSas($url)
    {
        $url = parse_url($url);
        return $url['scheme'] . "://" . $url['host'] . $url['path'];
    }
}
