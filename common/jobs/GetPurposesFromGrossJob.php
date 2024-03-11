<?php
namespace common\jobs;

use common\models\OsagoRequest;
use common\models\Travel;
use common\models\TravelPurpose;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\queue\RetryableJobInterface;
use yii\web\BadRequestHttpException;

class GetPurposesFromGrossJob extends \yii\base\BaseObject implements RetryableJobInterface
{
    protected $attempt_times = 60;

    public function execute($queue)
    {
        $transaction = \Yii::$app->db->beginTransaction();

        try {
            $purposes_uz = ArrayHelper::map(OsagoRequest::sendTravelRequest(OsagoRequest::URLS['travel_purposes'], (new Travel()), ['lang' => 'uz'])['response'], 'id', 'name');

            $purposes_ru = ArrayHelper::map(OsagoRequest::sendTravelRequest(OsagoRequest::URLS['travel_purposes'], (new Travel()), ['lang' => 'ru'])['response'], 'id', 'name');

            $purposes_en = ArrayHelper::map(OsagoRequest::sendTravelRequest(OsagoRequest::URLS['travel_purposes'], (new Travel()), ['lang' => 'en'])['response'], 'id', 'name');

            if (empty($purposes_uz) or empty($purposes_ru) or empty($purposes_en))
                throw new BadRequestHttpException('purposes are empty');

            TravelPurpose::deleteAll();
            Yii::$app->db->createCommand("ALTER SEQUENCE travel_purpose_id_seq RESTART WITH 1")->execute();

            $gross_purposes = [];
            foreach ($purposes_uz as $id => $name_uz) {
                $gross_purposes[] = [
                    'name_uz' => $name_uz,
                    'name_ru' => $purposes_ru[$id],
                    'name_en' => $purposes_en[$id],
                    'status' => true,
                ];
            }

            Yii::$app->db->createCommand()->batchInsert('travel_purpose', ['name_uz', 'name_ru', 'name_en', 'status'], $gross_purposes)->execute();

            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
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