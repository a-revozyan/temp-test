<?php
namespace backapi\models\forms\warehouseForms;

use common\models\Partner;
use common\models\Product;
use common\models\Warehouse;
use yii\base\Model;


class CreateWarehouseForm extends Model
{
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
            [['series', 'number', 'status', 'partner_id'], 'required'],
            [['series', 'number'], 'string'],
            [['series', 'number'], 'filter', 'filter' => 'trim'],
            [['partner_id', 'status'], 'integer'],
            [['partner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Partner::className(), 'targetAttribute' => ['partner_id' => 'id']],
            [['status'], 'in', 'range' => Warehouse::STATUS],
        ];
    }

    public function save()
    {
        $warehouse = new Warehouse();
        $warehouse->series = $this->series;
        $warehouse->number = $this->number;
        $warehouse->status = $this->status;
        $warehouse->partner_id = $this->partner_id;
        $warehouse->product_id = Product::products['kasko'];
        $warehouse->save();

        return $warehouse;
    }

}