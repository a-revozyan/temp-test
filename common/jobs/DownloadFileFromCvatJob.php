<?php
namespace common\jobs;

use common\helpers\GeneralHelper;
use common\models\CarInspection;
use common\models\CvatLabel;
use FilesystemIterator;
use Yii;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;
use yii\queue\RetryableJobInterface;

class DownloadFileFromCvatJob extends \yii\base\BaseObject implements RetryableJobInterface
{
    public $job_id;
    public $frame_id;
    public $car_inspection_id;
    public $shapes;
    public $viewingAngles;
    protected $attempt_times = 60;

    public function execute($queue)
    {
        $client = new Client();
        $cvat_url = GeneralHelper::env('cvat_url');
        $headers = [
            'Authorization' => 'Basic ' . base64_encode(GeneralHelper::env('cvat_username') . ":" . GeneralHelper::env('cvat_password')),
            'Content-Type' => 'application/json',
        ];

        $frameResponse = $client->createRequest()
            ->setMethod('GET')
            ->setUrl("$cvat_url/api/jobs/$this->job_id/data?org=&quality=compressed&type=frame&number=$this->frame_id")
            ->addHeaders($headers)
            ->send();

        $imageData = $frameResponse->content;


        // Create image resource from image data
        $image = imagecreatefromstring($imageData);

        // we have to grab all labels and save it in our db so we don't need to make a request to CVAT server every time
        // http://cvat.sugurtabozor.uz/api/labels?job_id=2&org=&page_size=500&page=1

        $labels = CvatLabel::find()->asArray()->all();
        $labels = ArrayHelper::map($labels, 'label_id', 'color');
        // Loop through shapes and draw polygons on image
        foreach ($this->shapes as $shape) {
            $label_id = $shape['label_id'];
            $labelColor = $labels[$label_id];

            list($r, $g, $b) = sscanf($labelColor, "#%02x%02x%02x");

            $points = $shape['points'];
            $color = imagecolorallocate($image, $r, $g, $b);
//            imagesetthickness($image, 5);
            imagepolygon($image, $points, $color);
        }

        // Output image
        header('Content-Type: image/jpeg');
        // imagepng($image);

        $folder = Yii::$app->basePath . "/../saas/web/assets/cvat/output/" . $this->car_inspection_id;

        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        $image_path = "$folder/{$this->frame_id}_" . rand(0,9999) . ".JPEG";
        $rotate = imagerotate($image, 90, 0);
        imagejpeg($rotate, $image_path, 100);

        $car_inspection = CarInspection::find()->where(['id' => $this->car_inspection_id])->one();
        $file_paths_in_folder = array_diff(scandir($folder), array('.', '..'));

        $file_paths_in_folder = array_map(function ($file_path) use($folder){
            return $folder . "/$file_path";
        }, $file_paths_in_folder);

        if (count($file_paths_in_folder) == $car_inspection->frames_count)
            Yii::$app->queue1->push(new GenerateAndUploadPdfJob([
                'image_paths' => $file_paths_in_folder,
                'car_inspection_id' => $this->car_inspection_id,
                'folder' => $folder,
                'viewingAngles' => $this->viewingAngles,
            ]));
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