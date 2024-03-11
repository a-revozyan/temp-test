<?php
namespace backapi\models\forms\warehouseForms;

use common\models\Kasko;
use common\models\Partner;
use common\models\Travel;
use common\models\Warehouse;
use yii\base\Model;


class UpdateWarehouseForm extends Model
{
    public $warehouse_id;
    public $partner_id;
    public $status;
    public $series;
    public $number;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['warehouse_id', 'series', 'number', 'status', 'partner_id'], 'required'],
            [['series', 'number'], 'string'],
            [['series', 'number'], 'filter', 'filter' => 'trim'],
            [['warehouse_id', 'partner_id', 'status'], 'integer'],
            [['partner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Partner::className(), 'targetAttribute' => ['partner_id' => 'id']],
            [['warehouse_id'], 'exist', 'skipOnError' => true, 'targetClass' => Warehouse::className(), 'targetAttribute' => ['warehouse_id' => 'id']],
            [['status'], 'in', 'range' => Warehouse::STATUS],
        ];
    }

    public function save()
    {
        $warehouse = Warehouse::findOne($this->warehouse_id);
        $warehouse->series = $this->series;
        $warehouse->number = $this->number;
        $warehouse->status = $this->status;
        $warehouse->partner_id = $this->partner_id;
        $warehouse->save();

        /** @var Kasko $order */
        $order = Kasko::find()->where(['warehouse_id' => $warehouse->id])->one();
        if (!is_null($order))
        {
            $order->policy_number = $warehouse->series . " " . $warehouse->number;
            $order->save();
        }

        return $warehouse;
    }

}