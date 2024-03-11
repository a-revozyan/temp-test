<?php
namespace common\jobs;

use common\models\OsagoPrice;
use common\services\fond\FondService;
use yii\queue\RetryableJobInterface;
use yii\web\BadRequestHttpException;

class CalculateOsagoJob extends \yii\base\BaseObject implements RetryableJobInterface
{
    public $vehicle;
    public $use_territory;
    public $driver_limit;
    public $period;

    protected $attempt_times = 60;

    public function execute($queue)
    {
        $attributes = [
            'vehicle' => $this->vehicle,
            'use_territory' => $this->use_territory,
            'period' => $this->period,
            'driver_limit' => $this->driver_limit,
            'discount' => 1,
        ];
        $response_array = FondService::calc($attributes['vehicle'], $attributes['use_territory'], $attributes['period'], $attributes['driver_limit'], $attributes['discount'], true);

        if (!is_array($response_array) or !array_key_exists('prem', $response_array))
            throw new BadRequestHttpException();

        $amount = $response_array['prem'];

        $osago_price = OsagoPrice::find()->where($attributes)->one();
        if (empty($osago_price))
            $osago_price = new OsagoPrice();
        $osago_price->setAttributes($attributes);
        $osago_price->amount = $amount;
        $osago_price->updated_at = date('Y-m-d H:i:s');
        $osago_price->save();
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