<?php

namespace common\models;

use common\helpers\GeneralHelper;
use Yii;

/**
 * This is the model class for table "short_link".
 *
 * @property int $id
 * @property string|null $long_url
 * @property string|null $short_url
 * @property int|null $redirects_count
 * @property string|null $created_at
 * @property string|null $last_redirect_at
 */
class ShortLink extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'short_link';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['redirects_count'], 'default', 'value' => null],
            [['redirects_count'], 'integer'],
            [['created_at', 'last_redirect_at'], 'safe'],
            [['long_url'], 'string', 'max' => 2048],
            [['short_url'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'long_url' => 'Long Url',
            'short_url' => 'Short Url',
            'redirects_count' => 'Redirects Count',
            'created_at' => 'Created At',
            'last_redirect_at' => 'Last Redirect At',
        ];
    }

    public static function generateRandomString()
    {
        return GeneralHelper::generateRandomString([self::className(), 'short_url']);
    }

    public static function findOrCreate($long_url)
    {
        $short_link = self::find()->where(['long_url' => $long_url])->one();
        if (is_null($short_link))
        {
            $short_link = new self();
            $short_link->long_url = $long_url;
            $short_link->short_url = GeneralHelper::env('front_website_url') . "?short=" . self::generateRandomString();
            $short_link->created_at = date('Y-m-d H:i:s');
            $short_link->save();
        }

        return $short_link;
    }
}
