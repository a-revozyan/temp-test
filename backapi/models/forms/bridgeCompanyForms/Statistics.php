<?php
namespace backapi\models\forms\bridgeCompanyForms;

use backapi\models\User;
use common\models\Accident;
use common\models\NumberDrivers;
use common\models\Osago;
use common\models\Partner;
use common\models\PartnerMonthBridgeCompanyDivvy;
use common\models\Product;
use Yii;
use yii\base\Model;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;

class Statistics extends Model
{
    public $month;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['month'], 'required'],
            [['month'], 'date', 'format' => 'php:Y-m'],
        ];
    }

    public function save()
    {
        $user = User::findOne(Yii::$app->user->identity->getId());
        if (empty($user->bridgeCompany))
            throw new BadRequestHttpException('You are not bridge company');
        $bridgeCompany = $user->bridgeCompany;

        $begin_date = "$this->month-1 00:00:00";
        $end_date = date("Y-m-t 23:59:59", strtotime("$this->month-1"));

        $osago_product = Product::products['osago'];
        $osagos = Osago::find()->select(['count(id)', 'sum(amount_uzs)', 'partner_id', 'number_drivers_id', new Expression("$osago_product as product_id")])
            ->groupBy(['partner_id', 'number_drivers_id'])
            ->where([
                'and',
                ['>', 'payed_date', strtotime($begin_date)],
                ['<', 'payed_date',  strtotime($end_date)],
                ['=', 'bridge_company_id',  $bridgeCompany->id],
            ])
            ->asArray()->all();

        $accident_product = Product::products['accident'];
        $accidents = Accident::find()->select(['count(accident.id)', 'sum(accident.amount_uzs)', 'accident.partner_id', new Expression('null::integer as number_drivers_id'), new Expression("$accident_product as product_id")])
            ->leftJoin('osago', 'osago.id = accident.osago_id')
            ->groupBy(['accident.partner_id', 'number_drivers_id'])
            ->where([
                'and',
                ['>', 'accident.payed_date', $begin_date],
                ['<', 'accident.payed_date',  $end_date],
                ['=', 'osago.bridge_company_id',  $bridgeCompany->id],
            ])
            ->asArray()->all();

        $policies  = array_merge($osagos, $accidents);

        //yordamchi ma'lumotlar
        $partners = [];
        $_partners = Partner::find()->all();
        foreach ($_partners as $partner) {
            $partners[$partner->id] = $partner->getForIdNameArr();
        }
        $_number_drivers = NumberDrivers::find()->all();
        $number_drivers = [];
        foreach ($_number_drivers as $number_driver) {
            $number_drivers[$number_driver->id] = $number_driver->getShortArr();
        }
        $_products = Product::find()->all();
        $products = [];
        foreach ($_products as $product) {
            $products[$product->id] = $product->getIdNameArr();
        }
        //yordamchi ma'lumotlar


        //foizlar
        $divvies = PartnerMonthBridgeCompanyDivvy::find()->select(['max(month) as month'])
            ->where([
                'and',
                ['<=', 'month', $this->month],
                ['=', 'bridge_company_id',  $bridgeCompany->id],
            ])
            ->groupBy(['partner_id', 'product_id', 'number_drivers_id'])->asArray()->all();

        $divvies = PartnerMonthBridgeCompanyDivvy::find()
            ->where([
                'month' => array_values(ArrayHelper::map($divvies, 'month', 'month')),
                'bridge_company_id' => $bridgeCompany->id
            ])
            ->asArray()->all();
        //foizlar

        $result = [];
        foreach ($policies as $policy) {
            $divvy_percent = $this->getDivvyPercent($divvies, $bridgeCompany->id, $policy['partner_id'], $policy['product_id'], $policy['number_drivers_id'], $this->month);
            $result[] = [
                'count' => $policy['count'],
                'sum' => $policy['sum'],
                'partner' => $partners[$policy['partner_id']],
                'product' => $products[$policy['product_id']],
                'number_drivers' => empty($policy['number_drivers_id']) ? null : $number_drivers[$policy['number_drivers_id']],
                'month' => $this->month,
                'divvy_percent' => $divvy_percent,
                'divvy_amount' => round($policy['sum'] * $divvy_percent / 100),
            ];
        }
        return $result;
    }

    public function getDivvyPercent($divvies, $bridge_company_id, $partner_id, $product_id, $number_drivers_id, $month)
    {
        foreach ($divvies as $divvy) {
            if (
                $divvy['bridge_company_id'] = $bridge_company_id
                and $divvy['partner_id'] = $partner_id
                and $divvy['product_id'] = $product_id
                and $divvy['number_drivers_id'] = $number_drivers_id
                and $divvy['month'] = $month
            )
                return $divvy['percent'];
        }

        return 0;
    }

}