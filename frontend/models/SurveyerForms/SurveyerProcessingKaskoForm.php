<?php

namespace frontend\models\SurveyerForms;

use backapi\models\forms\zoodPayForms\TransactionDeliveryForm;
use common\models\Kasko;
use common\models\KaskoFile;
use common\services\PaymentService;
use common\services\SMSService;
use Yii;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;

class SurveyerProcessingKaskoForm extends \yii\base\Model
{
    public $kasko_id;
    public $surveyer;
    public $surveyer_comment;
    public $docs;
    public $images;

    public function rules()
    {
        return [
            ['surveyer_comment', 'string', 'max' => 65535],
            [['kasko_id'], 'required'],
            ['kasko_id', 'integer'],
            ['kasko_id', 'exist', 'skipOnError' => true, 'targetClass' => Kasko::className(), 'targetAttribute' => ['kasko_id' => 'id'], 'filter' => ['surveyer_id' => $this->surveyer->id, 'status' => Kasko::STATUS['attached']]],
            [['docs', 'images'], 'each', "rule" => ['file', 'skipOnEmpty' => false,  'minFiles' => 1, 'maxSize' => 50 * 1024 * 1024]]
        ];
    }

    public function attributeLabels()
    {
        return [
            'kasko_id' => Yii::t('app', 'kasko'),
            'docs' => Yii::t('app', 'docs'),
            'images' => Yii::t('app', 'images'),
            'surveyer_comment' => Yii::t('app', 'surveyer comment'),
        ];
    }

    private function saveKaskoFiles($files, $type, $kasko)
    {
        foreach ($files as $file) {
            $folder_path = '/uploads/kasko-car-files/' . "$kasko->id-$kasko->insurer_passport_series$kasko->insurer_passport_number" . '/';
            if (\yii\helpers\FileHelper::createDirectory(Yii::getAlias('@webroot') . $folder_path, $mode = 0775, $recursive = true)) {
                $file_path = $folder_path . $file->baseName . "-" . Yii::$app->security->generateRandomString(5) . "." . $file->extension;
                if ($file->saveAs(Yii::getAlias('@webroot') . $file_path)) {
                    $kasko_file = new KaskoFile();
                    $kasko_file->path = $file_path;
                    $kasko_file->kasko_id = $kasko->id;
                    $kasko_file->type = $type;
                    $kasko_file->save();
                }
            }
        }
    }

    public function save()
    {
        $kasko = Kasko::findOne(['id' => $this->kasko_id]);

        $this->saveKaskoFiles($this->images, KaskoFile::TYPE['image'], $kasko);
        $this->saveKaskoFiles($this->docs, KaskoFile::TYPE['doc'], $kasko);

        $kasko->status = Kasko::STATUS['processed'];
        $kasko->processed_date = time();
        $kasko->surveyer_comment = $this->surveyer_comment;
        $kasko->surveyer_amount = $this->surveyer->service_amount;

        $kasko->save();

        SMSService::sendMessage($kasko->fUser->phone, Yii::t('app', "Sug'urta Bozor: Vash polis sformirovan. Pereydite po ssilke: https://bit.ly/3wYET86 \n
Polisingiz tayyor. Havola orqali o'ting"));

        if ($kasko->trans->payment_type == PaymentService::PAYMENT_TYPE['zoodpay'])
        {
            $delivery_form = new TransactionDeliveryForm();
            $delivery_form->model_class = "Kasko";
            $delivery_form->model_id = $kasko->id;
            $delivery_form->save();
        }

        return $kasko;
    }
}
