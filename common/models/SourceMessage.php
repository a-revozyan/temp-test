<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "source_message".
 *
 * @property int $id
 * @property string|null $category
 * @property string|null $message
 *
 * @property Message[] $messages
 * @property Message $ru
 * @property Message $uz
 * @property Message $en
 */
class SourceMessage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    
    public static function tableName()
    {
        return 'source_message';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['message'], 'string'],
            [['category'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'category' => Yii::t('app', 'Category'),
            'message' => Yii::t('app', 'Message'),
        ];
    }

    /**
     * Gets query for [[Messages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMessages()
    {
        return $this->hasMany(Message::className(), ['id' => 'id']);
    }

    public function getUz()
    {
        return $this->hasOne(Message::className(), ['id' => 'id'])
            ->andOnCondition(['language' => 'uz']);
    }

    public function getRu()
    {
        return $this->hasOne(Message::className(), ['id' => 'id'])
            ->andOnCondition(['language' => 'ru']);
    }

    public function getEn()
    {
        return $this->hasOne(Message::className(), ['id' => 'id'])
            ->andOnCondition(['language' => 'en']);
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
            'message' => $this->message,
            'ru' => $this->ru->translation,
            'uz' => $this->uz->translation,
            'en' => $this->en->translation,
        ];
    }
}
