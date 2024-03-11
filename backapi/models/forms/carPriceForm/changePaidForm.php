<?php
namespace backapi\models\forms\carPriceForm;

use common\models\Partner;
use common\models\PartnerMonthCarPricePay;
use yii\base\Model;
class changePaidForm extends Model
{
    public $partner_id;
    public $month;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['partner_id', 'month'], 'required'],
            [['partner_id'], 'integer'],
            [['partner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Partner::className(), 'targetAttribute' => ['partner_id' => 'id']],
            [['month'], 'date', 'format' => 'php:Y-m'],
        ];

    }

    public function save()
    {
        $monthly_paid = PartnerMonthCarPricePay::find()
            ->where(['partner_id' => $this->partner_id])
            ->andWhere(['ilike', 'month', $this->month])
            ->one();

        if (empty($monthly_paid))
        {
            $monthly_paid = new PartnerMonthCarPricePay();
            $monthly_paid->partner_id = $this->partner_id;
            $monthly_paid->month = $this->month;
            $monthly_paid->is_paid = false;
        }

        $monthly_paid->updated_at = date('Y-m-d H:i:s');
        $monthly_paid->is_paid = !$monthly_paid->is_paid;
        $monthly_paid->save();

        return $monthly_paid->is_paid;
    }
}