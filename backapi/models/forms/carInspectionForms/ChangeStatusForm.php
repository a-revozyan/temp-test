<?php
namespace backapi\models\forms\carInspectionForms;

use common\helpers\GeneralHelper;
use common\models\CarInspection;
use common\models\CarInspectionFile;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;
use yii\web\BadRequestHttpException;

class ChangeStatusForm extends Model
{
    public $id;
    public $status;
    public $comment;
    public $types;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status'], 'required'],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => CarInspection::className(), 'targetAttribute' => ['id' => 'id'], 'filter' => function($query){
                return $query->where(['in', 'status', [CarInspection::STATUS['uploaded'], CarInspection::STATUS['rejected'], CarInspection::STATUS['problematic']]]);
            }],
            [['comment'], 'string'],
            [['status'], 'in', 'range' => [CarInspection::STATUS['rejected'], CarInspection::STATUS['confirmed_to_cvat']]],
            [['types'], 'each', 'rule' => ['in', 'range' => array_keys(CarInspection::TYPE_MESSAGES)]],
        ];
    }

    public function save()
    {
       $car_inspection = CarInspection::findOne($this->id);

       if ($this->status == CarInspection::STATUS['confirmed_to_cvat'])
       {
           $client = new Client();
           try {
               $response = $client->post(GeneralHelper::env('cvat_url') . "/api/tasks", json_encode([
                   'name' => $car_inspection->autonumber,
                   'project_id' => GeneralHelper::env('cvat_project_id'),
               ]), [
                   'Authorization' => 'Basic ' . base64_encode(GeneralHelper::env('cvat_username') . ":" . GeneralHelper::env('cvat_password')),
                   'Content-Type' => 'application/json',
               ])->send();
               $response_array = (array)json_decode($response->getContent());
           }catch (Exception $exception) {
               $response_array = $exception->getMessage();
           }

           if (!is_array($response_array))
               throw new BadRequestHttpException($response_array);

           $car_inspection->task_id = $response_array['id'];
           $car_inspection->save();

           $images = ArrayHelper::getColumn($car_inspection->getCarInspectionFiles()->where(['not', ['type' => CarInspectionFile::TYPE['video']]])->asArray()->all(), 'url');
           $images = array_map(function ($image){
               return CarInspectionFile::urlWithoutSas($image);
           }, $images);

           try {
               $response = $client->post(GeneralHelper::env('cvat_url') . "/api/tasks/" . $car_inspection->task_id . "/data", json_encode([
                   'image_quality' => 99,
                   'remote_files' => $images,
               ]), [
                   'Authorization' => 'Basic ' . base64_encode(GeneralHelper::env('cvat_username') . ":" . GeneralHelper::env('cvat_password')),
                   'Content-Type' => 'application/json',
               ])->send();

               $response_array = (array)json_decode($response->getContent());
           }catch (Exception $exception) {
               $response_array = $exception->getMessage();
           }

           if (!is_array($response_array))
               throw new BadRequestHttpException($response_array);
       }else{
           $car_inspection->sendInviteSms($this->comment, false, $this->types);

           $car_inspection_files = CarInspectionFile::find()->where(['type' => $this->types, 'car_inspection_id' => $car_inspection->id])->all();

           foreach ($car_inspection_files as $car_inspection_file) {
               CarInspection::deleteBlob($car_inspection_file->url);
               $car_inspection_file->delete();
           }
       }

       $car_inspection->status = $this->status;
       $car_inspection->status_comment = $this->comment;
       $car_inspection->save();

       return $car_inspection;
    }

}