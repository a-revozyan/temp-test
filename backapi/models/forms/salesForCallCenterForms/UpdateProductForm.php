<?php
namespace backapi\models\forms\salesForCallCenterForms;

use common\models\Product;
use common\models\Reason;
use yii\base\Model;
use yii\web\NotFoundHttpException;


class UpdateProductForm extends Model
{
    public $product;
    public $product_id;
    public $reason_id;
    public $comment;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product', 'product_id'], 'required'],
            [['comment'], 'string'],
            [['reason_id', 'product', 'product_id'], 'integer'],
            [['product'], 'in', 'range' => Product::products],
            [['reason_id'], 'exist', 'skipOnError' => true, 'targetClass' => Reason::className(), 'targetAttribute' => ['reason_id' => 'id']],
        ];
    }

    public function save()
    {
        $product = Product::models[$this->product]::findOne($this->product_id);
        if (is_null($product))
            throw new NotFoundHttpException("model id not found");

        $product->setAttributes($this->attributes);
        $product->save();

        return Product::getShortArrForCallCenter(Product::getProductsQuery()->andWhere(['product' => $this->product, 'product_id' => $this->product_id])->one());
    }

}