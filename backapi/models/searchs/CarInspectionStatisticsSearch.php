<?php

namespace backapi\models\searchs;

use common\models\CarInspection;
use yii\base\Model;

class CarInspectionStatisticsSearch extends Model
{
    public $begin_date;
    public $end_date;
    public $partner_id;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['begin_date'], 'required'],
            [['begin_date', 'end_date'], 'date', 'format' => 'php:Y-m-d'],
            [['partner_id'], 'integer'],
        ];
    }

    public function search($params = [])
    {
        $this->setAttributes($params);
        if (!$this->validate()) {
            return [];
        }
        if (empty($this->end_date))
            $this->end_date = date('Y-m-d');

        $beginning_of_month = $this->begin_date . " " . "00:00:00";
        $ending_of_month = $this->end_date . " " . "23:59:59";

        $car_inspection_query = CarInspection::find()
            ->where(['between', 'created_at', $beginning_of_month, $ending_of_month])
            ->andFilterWhere(['partner_id' => $this->partner_id]);

        return [
            'new' => (clone $car_inspection_query)->andWhere(['in', 'status', [CarInspection::STATUS['created']]])->count(),
            'processing' => (clone $car_inspection_query)->andWhere(['in', 'status', [CarInspection::STATUS['processing']]])->count(),
            'problematic' => (clone $car_inspection_query)->andWhere(['in', 'status', [CarInspection::STATUS['problematic']]])->count(),
            'confirmed_to_cvat' => (clone $car_inspection_query)->andWhere(['in', 'status', [CarInspection::STATUS['confirmed_to_cvat']]])->count(),
            'rejected' => (clone $car_inspection_query)->andWhere(['in', 'status', [CarInspection::STATUS['rejected']]])->count(),
            'uploaded' => (clone $car_inspection_query)->andWhere(['in', 'status', [CarInspection::STATUS['uploaded']]])->count(),
            'completed' => (clone $car_inspection_query)->andWhere(['in', 'status', [CarInspection::STATUS['completed']]])->count(),
            'sent_verification_sms' => (clone $car_inspection_query)->andWhere(['in', 'status', [CarInspection::STATUS['sent_verification_sms']]])->count(),
            'verified_by_client' => (clone $car_inspection_query)->andWhere(['in', 'status', [CarInspection::STATUS['verified_by_client']]])->count(),
        ];
    }
}
