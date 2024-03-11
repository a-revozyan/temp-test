<?php

namespace frontend\models\ProfileForms;

use common\helpers\DateHelper;
use common\models\Kasko;
use common\models\Osago;
use common\models\Product;
use yii\helpers\VarDumper;

class AddStrangerPolicy extends \yii\base\Model
{
    public $product;
    public $end_date;
    public $autonumber;
    public $tech_pass_series;
    public $tech_pass_number;

    public function rules()
    {
        return [
            [['product'], 'in', 'range' => [Product::products['osago'], Product::products['kasko']]],
            [['end_date'], 'date', 'format' => 'php:d.m.Y'],
            [['autonumber', 'tech_pass_series', 'tech_pass_number'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
        ];
    }

    public function save()
    {
        switch ($this->product){
            case Product::products['osago'] :
                $product = new Osago();
                $product->status = Osago::STATUS['stranger'];
                break;
            case Product::products['kasko'] :
                $product = new Kasko();
                $product->status = Osago::STATUS['stranger'];
                break;
        }

        $product->autonumber = $this->autonumber;
        $product->insurer_tech_pass_series = $this->tech_pass_series;
        $product->insurer_tech_pass_number = $this->tech_pass_number;
        $product->end_date = DateHelper::date_format($this->end_date, 'd.m.Y', 'Y-m-d');
        $product->created_at = time();
        $product->f_user_id = \Yii::$app->user->id;
        $product->save();

        return [
            'id' => $product->id,
            'product' => $this->product,
            'autonumber' => $product->autonumber,
            'begin_date' => null,
            'end_date' => $product->end_date,
            "autoname" => null,
        ];
    }
}