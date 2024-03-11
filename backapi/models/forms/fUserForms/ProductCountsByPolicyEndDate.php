<?php
namespace backapi\models\forms\fUserForms;

use common\models\Accident;
use common\models\Kasko;
use common\models\KaskoBySubscription;
use common\models\Osago;
use common\models\Product;
use common\models\Token;
use common\models\Travel;
use common\models\User;
use yii\base\Model;
use yii\db\Query;
use yii\helpers\VarDumper;

class ProductCountsByPolicyEndDate extends Model
{
    public $from_date;
    public $till_date;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['from_date', 'till_date'], 'required'],
            [['from_date', 'till_date'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }
    public function save()
    {
        $time_period_where = [
            'and',
            ['>=', 'end_date', $this->from_date],
            ['<=', 'end_date', $this->till_date]
        ];

        return [
            'sb' => [
                'osago' => self::getCounts(
                    Product::products['osago'],
                    $time_period_where,
                    [Osago::STATUS['received_policy']],
                ),
                'kasko' => self::getCounts(
                    Product::products['kasko'],
                    $time_period_where,
                    [Kasko::STATUS['policy_generated']],
                )
            ],
            'stranger' => [
                'osago' => self::getCounts(
                    Product::products['osago'],
                    $time_period_where,
                    [Osago::STATUS['stranger']],
                ),
                'kasko' => self::getCounts(
                    Product::products['kasko'],
                    $time_period_where,
                    [Kasko::STATUS['stranger']],
                )
            ],
        ];
    }

    private static function getCounts($product, $time_period_where, $statuses)
    {
        $table_name = Product::models[$product]::tableName();

        $all_osago_count = Product::models[$product]::find()
            ->where(['in', 'status', $statuses])
            ->andWhere($time_period_where)->count();

        $already_bought_osago_count = $all_osago_count - Product::models[$product]::find()
                ->rightJoin([
                    "max_end_date_table" => Product::models[$product]::find()->select([
                        "max(end_date) as max_end_date",
                        'autonumber',
                    ])
                        ->where(['in', 'status', $statuses])
                        ->groupBy('autonumber')
                ],
                    '"max_end_date_table"."autonumber" = "'. $table_name .'"."autonumber" and "max_end_date_table"."max_end_date" = "'. $table_name .'"."end_date"')
                ->where($time_period_where)
                ->andWhere(['in', 'status', $statuses])
                ->count();

        return [
                'all' => $all_osago_count,
                'already_bought' => $already_bought_osago_count,
        ];
    }

}