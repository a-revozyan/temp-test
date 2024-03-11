<?php

namespace common\models;

use common\helpers\GeneralHelper;
use Yii;

/**
 * This is the model class for table "kasko_file".
 *
 * @property int $id
 * @property int|null $kasko_id
 * @property string|null $path
 * @property int|null $type
 *
 * @property Kasko $kasko
 */
class KaskoFile extends \yii\db\ActiveRecord
{

    const TYPE = [
        'doc' => 1,
        'image' => 2,
    ];

    public function fields()
    {
        return ['id', 'path', 'type'];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kasko_file';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kasko_id', 'type'], 'default', 'value' => null],
            [['kasko_id', 'type'], 'integer'],
            [['path'], 'string', 'max' => 255],
            [['kasko_id'], 'exist', 'skipOnError' => true, 'targetClass' => Kasko::className(), 'targetAttribute' => ['kasko_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'kasko_id' => Yii::t('app', 'Kasko ID'),
            'path' => Yii::t('app', 'Path'),
            'type' => Yii::t('app', 'Type'),
        ];
    }

    /**
     * Gets query for [[Kasko]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKasko()
    {
        return $this->hasOne(Kasko::className(), ['id' => 'kasko_id']);
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
            'type' => $this->type,
            'path' => GeneralHelper::env('front_website_send_request_url') . $this->path,
        ];
    }
}
