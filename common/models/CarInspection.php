<?php

namespace common\models;

use common\helpers\DateHelper;
use common\helpers\GeneralHelper;
use common\jobs\DownloadFileFromCvatJob;
use common\jobs\SendMessageInTimePeriodJob;
use common\jobs\SendMessageJob;
use common\services\fond\FondService;
use common\services\TelegramService;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Blob\BlobSharedAccessSignatureHelper;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use thamtech\uuid\helpers\UuidHelper;
use Yii;
use yii\httpclient\Client;
use yii\web\BadRequestHttpException;

/**
 * This is the model class for table "car_inspection".
 *
 * @property int $id
 * @property string|null $uuid
 * @property int|null $client_id
 * @property string|null $tex_pass_series
 * @property string|null $tex_pass_number
 * @property string|null $autonumber
 * @property string|null $vin
 * @property int|null $partner_auto_model_id
 * @property int|null $partner_id
 * @property int|null $task_id
 * @property int|null $status
 * @property string|null $created_at
 * @property string|null $send_invite_sms_date
 * @property string|null $send_verification_sms_date
 * @property string|null $big_time_of_sending_sms
 * @property integer|null $verification_code
 * @property integer|null $sent_sms_count
 * @property integer|null $push_token
 * @property integer|null $service_amount
 * @property integer|null $runway
 * @property double|null $longitude
 * @property double|null $latitude
 * @property integer|null $frames_count
 * @property string|null $address
 * @property integer|null $year
 * @property string|null $verified_date
 *
 * @property PartnerAutoModel|null $autoModel
 * @property \common\models\Client|null $client
 * @property Partner|null $partner
 */
class CarInspection extends \yii\db\ActiveRecord
{
    public $status_comment;

    public const SVAT_CONTAINER_PATH = "https://cvatstoragegroup.blob.core.windows.net/cvat-container";

    public const STATUS = [
        'created' => 0,
        'processing' => 1,
        'uploaded' => 2,
        'rejected' => 3,
        'confirmed_to_cvat' => 4,
        'completed' => 5,
        'sent_verification_sms' => 6,
        'verified_by_client' => 7,
        'problematic' => 8,
    ];

    public const VIEWING_ANGLE = [
        'front' => 'Спереди',
        'behind' => 'Сзади',
        'right' => 'Справа',
        'left' => 'Слева',
        'top' => 'Сверху',
    ];

    public const LOCATION = [
        'Roof' => 'Крыша',
        'Hood' => 'Капот',
        'front_bumper' => 'Передний бампер',
        'rear_bumper' => 'Задний бампер',
        'right_mirror' => 'Правое зеркало',
        'left_mirror' => 'Левое зеркало',
        'windshield' => 'Лобовое стекло',
        'rear_glass' => 'Заднее стекло',
        'right_front_door' => 'Правая передняя дверь',
        'left_front_door' => 'Левая передняя дверь',
        'right_rear_door' => 'Правая задняя дверь',
        'left_rear_door' => 'Левая задняя дверь',
        'right_front_fender' => 'Правое переднее крыло',
        'left_front_fender' => 'Левое переднее крыло',
        'right_rear_fender' => 'Правое заднее крыло',
        'left_rear_fender' => 'Левое заднее крыло',
        'back_hood' => 'Багажник',
    ];

    public const TYPE_MESSAGES = [
        0 => 'video',
        1 => 'Vid speredi',
        2 => 'Vid speredi sleva',
        3 => 'Vid speredi sprava',
        4 => 'Vid szadi sleva',
        5 => 'Vid szadi sprava',
        6 => 'Krsha',
        7 => 'Lobovoe steklo',
        8 => 'Foto spidometra s probegom',
        9 => 'vin_code'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'car_inspection';
    }

    public function beforeSave($insert)
    {
        StatusHistory::create($this);
        return parent::beforeSave($insert);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['client_id', 'partner_auto_model_id', 'partner_id', 'task_id', 'status', 'verification_code', 'sent_sms_count', 'runway', 'frames_count', 'year'], 'default', 'value' => null],
            [['client_id', 'partner_auto_model_id', 'partner_id', 'task_id', 'status', 'verification_code', 'sent_sms_count', 'service_amount', 'runway', 'frames_count', 'year'], 'integer'],
            [['created_at', 'send_invite_sms_date', 'send_verification_sms_date', 'big_time_of_sending_sms', 'push_token', 'longitude', 'latitude', 'verified_date'], 'safe'],
            [['uuid', 'autonumber', 'vin'], 'string', 'max' => 255],
            [['address'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uuid' => 'Uuid',
            'client_id' => 'Client ID',
            'autonumber' => 'Autonumber',
            'vin' => 'Vin',
            'partner_auto_model_id' => 'Partner Auto Model ID',
            'partner_id' => 'Partner ID',
            'task_id' => 'Task ID',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
    }

    public static function getStatusLabel($status)
    {
        switch ($status)
        {
            case CarInspection::STATUS['created'] :
                $status = 'Создан';
                break;
            case CarInspection::STATUS['processing'] :
                $status = 'Начинать';
                break;
            case CarInspection::STATUS['uploaded'] :
                $status = 'Загружено';
                break;
            case CarInspection::STATUS['rejected'] :
                $status = 'БРАК';
                break;
            case CarInspection::STATUS['confirmed_to_cvat'] :
                $status = 'переведен в CVAT';
                break;
            case CarInspection::STATUS['completed'] :
                $status = 'Сгенерирован Акт';
                break;
            case CarInspection::STATUS['sent_verification_sms'] :
                $status = 'Отправлено смс с подтверждением';
                break;
            case CarInspection::STATUS['verified_by_client'] :
                $status = 'Подписан';
                break;
            case CarInspection::STATUS['problematic'] :
                $status = 'Проблемный';
                break;
            default :
                $status = "not found";
        }

        return $status;
    }

    public function monitoring_url(): string
    {
        return GeneralHelper::env('saas_front_site_url') . '/uz/' . $this->uuid;
    }

    public function notify_client($body)
    {
        Yii::$app->queue1->push(new SendMessageJob(['phone' => $this->client->phone, 'message' => $body]));

//        $channelName = 'user_' . $this->id;
//
//        $recipient = 'ExponentPushToken['. $this->push_token .']';
//
//        $expo = Expo::normalSetup();
//        $expo->subscribe($channelName, $recipient);
//
//        $notification = ['body' => $body, 'data'=> json_encode(array('uuid' => $this->uuid))];
//        $expo->notify([$channelName], $notification);
    }

    public function sendInviteSms($comment = "", $created_by_admin = false, $types = [])
    {
        if (empty($comment))
            $comment = "";

        $url = $this->monitoring_url();
        if (!empty($types))
        {
            if (in_array(0, $types))
                $url = "$url/7-step";
            else
                $url = "$url/9-step";
            $url .= "?types=" . implode(',', $types);

            $file_types = implode(", ", array_filter(CarInspection::TYPE_MESSAGES, function($index) use ($types) {
                return in_array($index, $types);
            }, ARRAY_FILTER_USE_KEY));

            $message = Yii::t('app', "{partner}! Shu silkaga kirib video va rasmlarni qayta yuklang: {url}! {comment}! Nedostayushiy fragmenti: {file_types}!",
                ['partner' => $this->partner->name, 'url' => $url, 'comment' => $comment, 'file_types' => $file_types]);
        }else{
            $message = Yii::t('app', "{partner}! Shu silkaga kirib video va rasmlarni yuklang: {url}!", ['partner' => $this->partner->name, 'url' => $url]);
        }

        if ($created_by_admin)
            Yii::$app->queue1->push(new SendMessageInTimePeriodJob([
                'phone' => $this->client->phone,
                'message' => $message
            ]));
        else
            Yii::$app->queue1->push(new SendMessageJob([
                'phone' => $this->client->phone,
                'message' => $message
            ]));


        $this->send_invite_sms_date = date('Y-m-d H:i:s');
        $this->save();
    }

    public static function create($form, $created_by_admin = false)
    {
        $total_amount = PartnerAccount::find()->where(['partner_id' => $form->partner_id])->sum('amount');
        $used_amount = CarInspection::find()->where(['partner_id' => $form->partner_id, 'status' => CarInspection::STATUS['verified_by_client']])->sum('service_amount');
        $partner_service_amount = Partner::findOne($form->partner_id)->service_amount;

        if (empty($partner_service_amount))
            throw new BadRequestHttpException('Service amount is empty for you. please tell us about it');

        if (($total_amount - $used_amount)/$partner_service_amount <= GeneralHelper::env('max_debt_car_inspections'))
            throw new BadRequestHttpException('You have used up your limit');

        if (isset($form->vin) and isset($form->auto_model) and isset($form->name))
            $auto_info = [
                'ORGNAME' => $form->name,
                'MODEL_NAME' => $form->auto_model,
                'BODY_NUMBER' => $form->vin,
                'ISSUE_YEAR' => $form->year,
            ];
        else
            $auto_info = FondService::getAutoInfo($form->tex_pass_series, $form->tex_pass_number, $form->autonumber, true);

//        VarDumper::dump($auto_info, 100, true);die();

        if (!$client = \common\models\Client::find()->where(['phone' => $form->phone, 'name' => $auto_info['ORGNAME']])->one())
        {
            $client = new \common\models\Client();
            $client->name = $auto_info['ORGNAME'];
            $client->phone = $form->phone;
            $client->created_at = date('Y-m-d H:i:s');
            $client->save();
        }

        if (!$partner_auto_brand = PartnerAutoBrand::findOne(['name' => "Not found"]))
        {
            $partner_auto_brand = new PartnerAutoBrand();
            $partner_auto_brand->name = "Not found";
            $partner_auto_brand->created_at = date('Y-m-d H:i:s');
            $partner_auto_brand->created_by_saas = true;
            $partner_auto_brand->save();
        }

        if (!$partner_auto_model = PartnerAutoModel::findOne(['name' => $auto_info['MODEL_NAME'], 'partner_auto_brand_id' => $partner_auto_brand->id]))
        {
            $partner_auto_model = new PartnerAutoModel();
            $partner_auto_model->name = $auto_info['MODEL_NAME'];
            $partner_auto_model->partner_auto_brand_id = $partner_auto_brand->id;
            $partner_auto_model->created_at = date('Y-m-d H:i:s');
            $partner_auto_model->created_by_saas = true;
            $partner_auto_model->save();
        }

        $car_inspection = new CarInspection();
        $car_inspection->uuid = UuidHelper::uuid();
        $car_inspection->tex_pass_series = $form->tex_pass_series;
        $car_inspection->tex_pass_number = $form->tex_pass_number;
        $car_inspection->autonumber = $form->autonumber;
        $car_inspection->vin = $auto_info['BODY_NUMBER'];
        $car_inspection->partner_auto_model_id = $partner_auto_model->id;
        $car_inspection->partner_id = $form->partner_id;
        $car_inspection->service_amount = Partner::findOne($form->partner_id)->service_amount;
        $car_inspection->client_id = $client->id;
        $car_inspection->created_at = date('Y-m-d H:i:s');
        $car_inspection->year = $auto_info['ISSUE_YEAR'];
        $car_inspection->status = CarInspection::STATUS['created'];
        $car_inspection->save();

        $car_inspection->sendInviteSms('', $created_by_admin);
        TelegramService::send($car_inspection);

        return $car_inspection;
    }

    public function getAutoModel()
    {
        return $this->hasOne(PartnerAutoModel::class, ['id' => 'partner_auto_model_id']);
    }

    public function getClient()
    {
        return $this->hasOne(\common\models\Client::class, ['id' => 'client_id']);
    }

    public function getPartner()
    {
        return $this->hasOne(Partner::class, ['id' => 'partner_id']);
    }

    public function getCarInspectionFiles()
    {
        return $this->hasMany(CarInspectionFile::class, ['car_inspection_id' => 'id']);
    }

    public function getActInspection()
    {
//        if (!in_array($this->status, [self::STATUS['completed'], self::STATUS['sent_verification_sms'], self::STATUS['verified_by_client']]))
//            return 0;

        $cvat_url = GeneralHelper::env('cvat_url');
        $headers = [
            'Authorization' => 'Basic ' . base64_encode(GeneralHelper::env('cvat_username') . ":" . GeneralHelper::env('cvat_password')),
            'Content-Type' => 'application/json',
        ];

        // We get annotation for a job
        $client = new Client();

        $jobResponse = $client->createRequest()
            ->setMethod('GET')
            ->setUrl("$cvat_url/api/jobs")
            ->addHeaders($headers)
            ->setData(['task_id' => $this->task_id])
            ->send();

        if (!$jobResponse->isOk or !$data = json_decode($jobResponse->content, true) or empty($data['results']))
            return 0;

        $job_id = $data['results'][0]['id'];
        $annotationResponse = $client->createRequest()
            ->setMethod('GET')
            ->setUrl("$cvat_url/api/jobs/$job_id/annotations")
            ->addHeaders($headers)
            ->send();

        if (!$annotationResponse->isOk)
            return 0;

        $data = json_decode($annotationResponse->content, true);
        $shapes = $data['shapes'];

        $shapesByFrame = array();
        $viewingAngles = array();

        foreach ($shapes as $shape) {
            $frameId = $shape['frame'];
            $shapesByFrame[$frameId][] = $shape;

            $attributes = $shape['attributes'];
            $viewing_angle_index = in_array($attributes[0]['value'], self::VIEWING_ANGLE) ? 0 : 1;
            $viewing_angle = $attributes[$viewing_angle_index]['value'];
            $location = $attributes[!$viewing_angle_index]['value'];
            $viewingAngles[$viewing_angle][$location][] = $shape['label_id'];

        }

//        $this->frames_count = count($shapesByFrame);
        $this->frames_count = CarInspectionFile::FILES_COUNT-1;
        $this->save();

        // Loop through shapes by frame ID and draw polygons on images
        $frames = range(0, CarInspectionFile::FILES_COUNT-2);
        foreach ($frames as $frameId) {
            // Get image data
            Yii::$app->queue1->push(new DownloadFileFromCvatJob([
                'job_id' => $job_id,
                'frame_id' => $frameId,
                'car_inspection_id' => $this->id,
                'shapes' => $shapesByFrame[$frameId] ?? [],
                'viewingAngles' => $viewingAngles,
            ]));
        }
    }

    public static function getBlobUrl($blob)
    {
        $container = GeneralHelper::env('azure_file_container');
        $azure_account_name = GeneralHelper::env('azure_account_name');
        $azure_account_key = GeneralHelper::env('azure_account_key');

        $sas_helper = new BlobSharedAccessSignatureHelper($azure_account_name, $azure_account_key);

        $sas = $sas_helper->generateBlobServiceSharedAccessSignatureToken(
            Resources::RESOURCE_TYPE_BLOB,              # Resource name to generate the canonicalized resource. It can be Resources::RESOURCE_TYPE_BLOB or Resources::RESOURCE_TYPE_CONTAINER
            "{$container}/{$blob}",                     # The name of the resource, including the path of the resource. It should be {container}/{blob}: for blobs.
            "racwd",                                    # Signed permissions.
            (new \DateTime())->modify('+20 minute'),    # Signed expiry
            (new \DateTime())->modify('-5 minute'),     # Signed start
            '',                                         # Signed IP, the range of IP addresses from which a request will be accepted, eg. "168.1.5.60-168.1.5.70"
            'https'                                    # Signed protocol, should always be https
        );

        return "https://{$azure_account_name}.blob.core.windows.net/{$container}/{$blob}?{$sas}";
    }

    public static function deleteBlob($url)
    {
        try {
            $container = GeneralHelper::env('azure_file_container');
            $azure_account_name = GeneralHelper::env('azure_account_name');
            $azure_account_key = GeneralHelper::env('azure_account_key');

            $connectionString = "DefaultEndpointsProtocol=https;AccountName=$azure_account_name;AccountKey=$azure_account_key;EndpointSuffix=core.windows.net";
            $blobClient = BlobRestProxy::createBlobService($connectionString);

            $blobClient->deleteBlob($container, explode('/', parse_url($url)['path'])[2]);
        }catch (\Exception $e){

        }
    }

    public function seconds_till_next_verification_sms()
    {
        $seconds = 0;
        $big_time_of_sending_sms_in_seconds = is_null($this->big_time_of_sending_sms) ? 0 : strtotime($this->big_time_of_sending_sms);
        $time_of_sending_sms_in_seconds = is_null($this->send_verification_sms_date) ? 0 : strtotime($this->send_verification_sms_date);
        $now_in_seconds = strtotime(\date('Y-m-d H:i:s'));

        if ($now_in_seconds - $time_of_sending_sms_in_seconds < GeneralHelper::env('saas_time_intervel_sending_sms_in_seconds'))
            $seconds = GeneralHelper::env('saas_time_intervel_sending_sms_in_seconds') + $time_of_sending_sms_in_seconds - $now_in_seconds;

        if (
            !empty($this->sent_sms_count)
            and $this->sent_sms_count % GeneralHelper::env('sms_count_for_big_interval') == 0
            and $now_in_seconds - $big_time_of_sending_sms_in_seconds < GeneralHelper::env('big_time_intervel_sending_sms_in_seconds')
        )
            $seconds = GeneralHelper::env('big_time_intervel_sending_sms_in_seconds') + $big_time_of_sending_sms_in_seconds - $now_in_seconds;

        return $seconds;
    }

    public static function getShortArrCollection($models)
    {
        $_models = [];
        foreach ($models as $model) {
            $_models[] = $model->getShortArr();
        }

        return $_models;
    }

    public function getFullArr()
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'autonumber' => $this->autonumber,
            'vin' => $this->vin,
            'runway' => $this->runway,
            'tex_pass_series' => $this->tex_pass_series,
            'tex_pass_number' => $this->tex_pass_number,
            'auto_model' => !empty($this->autoModel) ? $this->autoModel->getWithBrand() : null,
            'client' => $this->client->getShortArr(),
            'status' => $this->status,
            'created_at' => !empty($this->created_at) ? DateHelper::date_format($this->created_at, 'Y-m-d H:i:s', 'd/m/Y H:i:s') : null,
            'send_invite_sms_date' => !empty($this->send_invite_sms_date) ? DateHelper::date_format($this->send_invite_sms_date, 'Y-m-d H:i:s', 'd/m/Y H:i:s') : null,
            'send_verification_sms_date' => !empty($this->send_verification_sms_date) ? DateHelper::date_format($this->send_verification_sms_date, 'Y-m-d H:i:s', 'd/m/Y H:i:s') : null,
            'car_inspection_files' => CarInspectionFile::getShortArrCollection($this->carInspectionFiles),
            'pdf_url' => self::SVAT_CONTAINER_PATH . "/" . $this->uuid . ".pdf",
            'seconds_till_next_verification_sms' => $this->seconds_till_next_verification_sms(),
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'address' => $this->address,
            'year' => $this->year,
            'monitoring_url' => $this->monitoring_url(),
        ];
    }

    public function getShortArr()
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'autonumber' => $this->autonumber,
            'vin' => $this->vin,
            'runway' => $this->runway,
            'auto_model' => !empty($this->autoModel) ? $this->autoModel->getWithBrand() : null,
            'client' => $this->client->getShortArr(),
            'status' => [
                'id' => $this->status,
                'name' => self::getStatusLabel($this->status)
            ],
            'created_at' => !empty($this->created_at) ? DateHelper::date_format($this->created_at, 'Y-m-d H:i:s', 'd.m.Y H:i:s') : null,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'address' => $this->address,
            'year' => $this->year,
        ];
    }

}
