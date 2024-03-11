<?php
namespace backapi\models\forms\promoForms;

use common\helpers\DateHelper;
use common\models\Product;
use common\models\Promo;
use yii\base\Model;
use yii\helpers\VarDumper;

class UpdatePromoForm extends Model
{
    public $id;
    public $code;
    public $amount;
    public $begin_date;
    public $end_date;
    public $status;
    public $number;
    public $amount_type;
    public $product_ids;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [["code", "amount", "amount_type", "id"], 'required'],
            [['code'], 'string'],
            [['code'], 'filter', 'filter' => 'trim'],
            [['amount', 'status', 'number'], 'integer'],
            [['status'], 'in', 'range' => Promo::STATUS],
            [['amount_type'], 'in', 'range' => Promo::AMOUNT_TYPE],
            [['begin_date', 'end_date'], 'date', 'format' => 'php:d.m.Y'],
            [['product_ids'], 'each', 'rule' => ['integer']],
            [['product_ids'], 'default', 'value' => [], "skipOnEmpty" =>false],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => Promo::className(), 'targetAttribute' => ['id' => 'id']],
        ];
    }

    public function save()
    {
        $promo = Promo::findOne($this->id);
        $promo->setAttributes($this->attributes);
        $promo->amount = -1 * abs($promo->amount);
        $promo->begin_date = is_null($this->begin_date) ? null : DateHelper::date_format($this->begin_date, 'd.m.Y', 'Y-m-d');
        $promo->end_date = is_null($this->end_date) ? null : DateHelper::date_format($this->end_date, 'd.m.Y', 'Y-m-d');
        $promo->save();

        $products = Product::find()->where(['in', 'id', $this->product_ids])->all();
        $promo->unlinkAll('products');
        foreach ($products as $product) {
            $promo->link('products', $product);
        }
        $promo->save();

        return $promo;
    }

}