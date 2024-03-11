<?php
namespace common\jobs;

use common\models\Accident;
use common\models\AccidentPrice;
use common\models\KapitalSugurtaRequest;
use common\models\OsagoRequest;
use yii\queue\RetryableJobInterface;
use yii\web\BadRequestHttpException;

class CalculateAccidentPriceJob extends \yii\base\BaseObject implements RetryableJobInterface
{
    protected $attempt_times = 60;

    public function execute($queue)
    {
        //gross accident price
        $response = OsagoRequest::sendAccidentRequest(OsagoRequest::URLS['health_calc_sb'], (new Accident()), [
            "program_id" => Accident::DEFAULT_PROGRAM_ID,
            "insurer_count" => 1,
            "insurance_amount" => Accident::DEFAULT_INSURANCE_AMOUNT,
        ]);

        if (!(is_array($response) and array_key_exists('response', $response) and $response['response'] and $response['response']->amount))
            throw new BadRequestHttpException();

        $gross_amount = $response['response']->amount;

        //kapital accident price
        $response = KapitalSugurtaRequest::sendRequest(KapitalSugurtaRequest::URLS['get_doc_types'], (new Accident()), []);

        if (!is_array($response))
            throw new BadRequestHttpException();

        foreach ($response as $doc_type) {
            if ($doc_type->ID == Accident::DEFAULT_DOC_TYPE_ID)
            {
                $kapital_amount = $doc_type->PREM;
                break;
            }
        }

        if (!isset($kapital_amount))
            throw new BadRequestHttpException();

        $accident_price = AccidentPrice::find()->one();
        if (empty($accident_price))
            $accident_price = new AccidentPrice();
        $accident_price->gross = $gross_amount;
        $accident_price->kapital = $kapital_amount;
        $accident_price->updated_at = date('Y-m-d H:i:s');
        $accident_price->save();
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