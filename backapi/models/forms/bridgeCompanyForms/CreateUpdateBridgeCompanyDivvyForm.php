<?php
namespace backapi\models\forms\bridgeCompanyForms;

use common\models\BridgeCompany;
use common\models\Partner;
use common\models\PartnerMonthBridgeCompanyDivvy;
use common\models\Product;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\helpers\VarDumper;


class CreateUpdateBridgeCompanyDivvyForm extends Model
{
    public $bridge_company_id;
    public $partner_id;
    public $product_id;
    public $number_drivers_id;
    public $month;
    public $percent;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bridge_company_id', 'partner_id', 'product_id', 'month', 'percent'], 'required'],
            [['bridge_company_id', 'partner_id', 'product_id', 'number_drivers_id'], 'integer'],
            [['percent'], 'double', 'min' => 0, 'max' => 100],
            [['bridge_company_id'], 'exist', 'skipOnError' => true, 'targetClass' => BridgeCompany::className(), 'targetAttribute' => ['bridge_company_id' => 'id']],
            [['partner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Partner::className(), 'targetAttribute' => ['partner_id' => 'id']],
            [['product_id'], 'in', 'range' => [Product::products['osago'], Product::products['accident']]],
            [['month'], 'date', 'format' => 'Y-m'],
        ];
    }

    public function save()
    {
        $attrs = [
            'bridge_company_id' => $this->bridge_company_id,
            'partner_id' => $this->partner_id,
            'product_id' => $this->product_id,
            'number_drivers_id' => $this->number_drivers_id,
            'month' => $this->month
        ];

        $bridge_company_divvy = PartnerMonthBridgeCompanyDivvy::find()->where($attrs)->one();
        if (empty($bridge_company_divvy))
            $bridge_company_divvy = new PartnerMonthBridgeCompanyDivvy();

        $attrs = array_merge($attrs, ['percent' => $this->percent]);

        $bridge_company_divvy->setAttributes($attrs);

        $bridge_company_divvy->save();

        return $bridge_company_divvy;
    }
}