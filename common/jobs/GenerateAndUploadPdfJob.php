<?php
namespace common\jobs;

use common\helpers\GeneralHelper;
use common\helpers\PdfHelper;
use common\models\CarInspection;
use Yii;
use yii\httpclient\Client;
use yii\queue\RetryableJobInterface;

class GenerateAndUploadPdfJob extends \yii\base\BaseObject implements RetryableJobInterface
{
    public $image_paths;
    public $viewingAngles;
    public $car_inspection_id;
    public $folder;

    protected $attempt_times = 60;

    public function execute($queue)
    {
        $file = PdfHelper::genAktOsmotr($this->car_inspection_id, $this->image_paths, $this->viewingAngles)->render();
        $this->deleteFolder();

        //save pdf to azureBlobStorage
        $car_inspection = CarInspection::find()->where(['id' => $this->car_inspection_id])->one();
        $client = new Client();
        $blob = "$car_inspection->uuid.pdf";
        $pdf_path = Yii::$app->basePath . "/../saas/web/assets/cvat/output/$blob";

        file_put_contents($pdf_path, $file);

        $pdf_url = CarInspection::getBlobUrl($blob);
        $client->createRequest()
            ->setMethod('put')
            ->setUrl($pdf_url)
            ->setHeaders([
                'Content-Type' => 'multipart/form-data',
                'x-ms-blob-type' => 'BlockBlob',
            ])
            ->addFile('file',$pdf_path)
            ->send();

        unlink($pdf_path);
        //save pdf to azureBlobStorage

        $verification_url =  GeneralHelper::env('saas_front_site_url') . '/ru/' . $car_inspection->uuid . '/document';
        $car_inspection->notify_client(Yii::t('app', "Tabriklaymiz, sizning akt osmotiringiz tayyor bo'ldi. quyidagi linkka kiring: ") . $verification_url);
    }

    public function deleteFolder()
    {
        //Get a list of all of the file names in the folder.
        $files = glob($this->folder . '/*');

        //Loop through the file list.
        foreach($files as $file){
            //Make sure that this is a file and not a directory.
            if(is_file($file)){
                //Use the unlink function to delete the file.
                unlink($file);
            }
        }

        rmdir($this->folder);
    }

    public function getTtr()
    {
        return 120;
    }

    public function canRetry($attempt, $error)
    {
        return  $attempt < $this->attempt_times;
    }
}