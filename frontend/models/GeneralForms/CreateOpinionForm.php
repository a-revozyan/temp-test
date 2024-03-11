<?php

namespace frontend\models\GeneralForms;

use common\helpers\GeneralHelper;
use common\models\Opinion;
use common\models\Osago;
use common\models\OsagoRequest;
use common\services\TelegramService;
use Yii;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;

class CreateOpinionForm extends \yii\base\Model
{
    public $name;
    public $phone;
    public $message;

    public function rules()
    {
        return [
            [['name', 'phone', 'message'], 'required'],
            [['message'], 'string'],
            [['name', 'phone'], 'string', 'max' => 255],
        ];
    }

    public function save()
    {
        $opinion = new Opinion();
        $opinion->setAttributes($this->attributes);
        $opinion->created_at = date('Y-m-d H:i:s');
        $opinion->save();

        return $opinion;
    }
}