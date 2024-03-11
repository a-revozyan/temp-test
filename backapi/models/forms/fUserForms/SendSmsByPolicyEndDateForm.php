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
use common\services\SMSService;
use yii\base\Model;
use yii\db\Query;
use yii\helpers\VarDumper;

class SendSmsByPolicyEndDateForm extends Model
{
    public $from_date;
    public $till_date;
    public $product;
    public $text;
    public $type;

    public const TYPE = [
        'sb' => 0,
        'stranger' => 1,
    ];

    public const statuses = [
        Product::products['osago'] => [
            self::TYPE['sb'] => [Osago::STATUS['received_policy']],
            self::TYPE['stranger'] => [Osago::STATUS['stranger']],
        ],
        Product::products['kasko'] => [
            self::TYPE['sb'] => [Kasko::STATUS['policy_generated']],
            self::TYPE['stranger'] => [Kasko::STATUS['stranger']],
        ]
    ];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['from_date', 'till_date', 'product', 'text', 'type'], 'required'],
            [['from_date', 'till_date'], 'date', 'format' => 'php:Y-m-d'],
            [['product'], 'in', 'range' => [Product::products['osago'], Product::products['kasko']]],
            [['type'], 'in', 'range' => self::TYPE],
        ];
    }

    public function save()
    {
        $time_period_where = [
            'and',
            ['>=', 'end_date', $this->from_date],
            ['<=', 'end_date', $this->till_date]
        ];

        $table_name = Product::models[$this->product]::tableName();
        $statuses = self::statuses[$this->product][$this->type];

        $products = Product::models[$this->product]::find()
            ->select(["f_user.phone as phone"])
            ->rightJoin([
                "max_end_date_table" => Product::models[$this->product]::find()->select([
                    "max(end_date) as max_end_date",
                    'autonumber',
                ])
                    ->where(['status' => $statuses])
                    ->groupBy('autonumber')
            ],
                '"max_end_date_table"."autonumber" = "' . $table_name . '"."autonumber" and "max_end_date_table"."max_end_date" = "' . $table_name . '"."end_date"')
            ->leftJoin('f_user', 'f_user.id = ' . $table_name . '.f_user_id')
            ->where($time_period_where)
            ->andWhere([$table_name . '.status' => $statuses])
            ->groupBy("f_user.phone")
            ->asArray()->all();

        return  true;

        foreach ($products as $product) {
            SMSService::sendMessage($product['phone'], $this->text);
        }

        return true;
    }
}