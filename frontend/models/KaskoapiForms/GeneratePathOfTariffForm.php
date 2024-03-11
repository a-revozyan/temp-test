<?php

namespace frontend\models\KaskoapiForms;

use common\models\Autocomp;
use common\models\CarAccessory;
use common\models\Kasko;
use common\models\KaskoTariff;
use Yii;
use yii\web\NotFoundHttpException;

class GeneratePathOfTariffForm extends \yii\base\Model
{
    public $id;

    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id'], 'integer'],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => KaskoTariff::className(), 'targetAttribute' => ['id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'id'),
        ];
    }

    public function path()
    {
        $kasko_tariff = KaskoTariff::findOne($this->id);
        if (is_file(\Yii::getAlias('@backend') . '/web/' . $kasko_tariff->file))
            return \Yii::$app->response->sendFile(\Yii::getAlias('@backend') . '/web/' . $kasko_tariff->file, basename($kasko_tariff->file), ['inline'=>true]);

        throw new NotFoundHttpException('File not found');
    }
}