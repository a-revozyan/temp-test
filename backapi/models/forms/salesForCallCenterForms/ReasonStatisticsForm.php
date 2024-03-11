<?php
namespace backapi\models\forms\salesForCallCenterForms;

use common\helpers\GeneralHelper;
use common\models\Product;
use common\models\Reason;
use yii\base\Model;
use yii\helpers\ArrayHelper;


class ReasonStatisticsForm extends Model
{
    public $from_date;
    public $till_date;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['from_date', 'till_date'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function save()
    {
        GeneralHelper::checkPermission();

        $reason_counts = Product::getProductsQuery()
            ->select(['reason_id', 'count(*)'])
            ->andWhere(['not', ['reason_id' => null]]);

        if (!empty($this->from_date))
            $reason_counts->andWhere(['>', 'policy_generated_date',  date_create_from_format('Y-m-d H:i:s', date($this->from_date . " 00:00:00"))->getTimestamp()]);
        if (!empty($this->till_date))
            $reason_counts->andWhere(['<', 'policy_generated_date',  date_create_from_format('Y-m-d H:i:s', date($this->till_date . " 23:59:59"))->getTimestamp()]);

        $reason_counts = $reason_counts->groupBy('reason_id')->all();

        $all_count = array_sum(ArrayHelper::getColumn($reason_counts, 'count'));

        $reason_percents = [];
        foreach ($reason_counts as $reason) {
            $reason_percents[] = [
                'reason_id' => $reason['reason_id'],
                'percent' => round($reason['count'] * 100 / $all_count),
            ];
        }

        $reasons = Reason::find()->where(['in', 'id', ArrayHelper::getColumn($reason_counts, 'reason_id')])->asArray()->all();
        return [
            'reason_percents' => $reason_percents,
            'reasons' => $reasons,
            'all_products_count' => $all_count,
        ];
    }
}