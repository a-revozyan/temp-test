<?php

namespace common\models;

use Yii;
use yii\helpers\VarDumper;
use yii\httpclient\Client;

/**
 * This is the model class for table "currency".
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property float|null $rate
 * @property string|null $rate_date
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Partner[] $partners
 */
class Currency extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'currency';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'code', 'created_at', 'updated_at'], 'required'],
            [['rate'], 'number'],
            [['rate_date'], 'safe'],
            [['created_at', 'updated_at'], 'default', 'value' => null],
            [['created_at', 'updated_at'], 'integer'],
            [['name', 'code'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'code' => Yii::t('app', 'Code'),
            'rate' => Yii::t('app', 'Rate'),
            'rate_date' => Yii::t('app', 'Rate Date'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[Partners]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPartners()
    {
        return $this->hasMany(Partner::className(), ['travel_currency_id' => 'id']);
    }

    public static function getUsdRate()
    {
        $currency = self::find()->where(['code' => 'usd', 'rate_date' => date('Y-m-d')])->one();

        if($currency) {
            return $currency->rate;
        } else {
            $client = new Client();
            $response = $client->createRequest()
                               ->setFormat(Client::FORMAT_JSON)
                               ->setMethod('POST')
                               ->setUrl('https://nbu.uz/en/exchange-rates/json/')
                               ->send();
            $data = json_decode($response->getContent(), true);
            $key = array_search('USD', array_column($data, 'code'));
            $usd = $data[$key]['cb_price'];

            $currency = self::find()->where(['code' => 'usd'])->one();
            $currency->rate = $usd;
            $currency->rate_date = date('Y-m-d');
            $currency->save();

            return $currency->rate;
        }
    }

    public static function getEuroRate()
    {
        $currency = self::find()->where(['code' => 'eu', 'rate_date' => date('Y-m-d')])->one();

        if($currency) {
            return $currency->rate;
        } else {
            $client = new Client();
            $response = $client->createRequest()
                ->setFormat(Client::FORMAT_JSON)
                ->setMethod('POST')
                ->setUrl('https://nbu.uz/en/exchange-rates/json/')
                ->send();
            $data = json_decode($response->getContent(), true);
            $key = array_search('EUR', array_column($data, 'code'));
            $eu = $data[$key]['cb_price'];

            $currency = self::find()->where(['code' => 'eu'])->one();
            $currency->rate = $eu;
            $currency->rate_date = date('Y-m-d');
            $currency->save();

            return $currency->rate;
        }
    }
}
