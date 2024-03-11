<?php
namespace saas\models\CarPrice;

use common\models\CarPriceRequest;
use common\models\PartnerAutoBrand;
use common\models\PartnerAutoModel;
use yii\base\Model;
use yii\web\BadRequestHttpException;

class CreateCarPriceRequestForm extends Model
{
    public $brand;
    public $model;
    public $type;
    public $engine;
    public $year;
    public $mileage;
    public $capacity;
    public $average_price;
    public $among_cars_count;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [["brand", "model", "type", "engine", "year", "mileage", "capacity", "average_price", "among_cars_count"], 'required'],
            [['brand', 'model', 'type', "engine"], 'string'],
            [[ "year", "mileage", "average_price", "among_cars_count"], 'integer'],
            [['capacity'], 'double'],
        ];
    }


    public function save()
    {
        $fuser = \Yii::$app->getUser()->identity;
        if (empty($fuser->partner))
        {
            $model = new CalculateReserveRequestsForm();
            if ($model->save()['reserve_requests'] < 1)
                throw new BadRequestHttpException('Your limit is over', 1);
        }

        $brand = PartnerAutoBrand::findOne(['name' => $this->brand, 'created_by_car_price_bot' => true]);
        if (is_null($brand))
        {
            $brand = new PartnerAutoBrand();
            $brand->name = $this->brand;
            $brand->created_by_car_price_bot = true;
            $brand->created_at = date('Y-m-d H:i:s');
            $brand->save();
        }

        $model = PartnerAutoModel::findOne(['name' => $this->model, 'created_by_car_price_bot' => true, 'partner_auto_brand_id' => $brand->id]);
        if (is_null($model))
        {
            $model = new PartnerAutoModel();
            $model->name = $this->model;
            $model->partner_auto_brand_id = $brand->id;
            $model->created_by_car_price_bot = true;
            $model->created_at = date('Y-m-d H:i:s');
            $model->save();
        }
        $car_price_request = new CarPriceRequest();
        $car_price_request->brand_id = $brand->id;
        $car_price_request->model_id = $model->id;
        $car_price_request->transmission_type = CarPriceRequest::TRANSMISSION_TYPE[$this->type];
        $car_price_request->fuel_type = CarPriceRequest::FUEL_TYPE[$this->engine];
        $car_price_request->year = $this->year;
        $car_price_request->mileage = $this->mileage;
        $car_price_request->engine_capacity = $this->capacity;
        $car_price_request->average_price = $this->average_price;
        $car_price_request->among_cars_count = $this->among_cars_count;
        $car_price_request->created_at = date('Y-m-d H:i:s');
        $car_price_request->fuser_id = $fuser->id;
        if (!empty($fuser->partner))
            $car_price_request->partner_id = $fuser->partner->id;
        $car_price_request->save();

        return true;
    }
}