<?php
namespace backapi\models\forms\autocompForms;

use backapi\models\User;
use common\models\Autobrand;
use common\models\Autocomp;
use common\models\Automodel;
use common\models\Partner;
use common\models\Product;
use Yii;
use yii\base\Model;


class CreateAutocompForm extends Model
{
    public $autobrand_name;
    public $automodel_name;
    public $name;
    public $production_year;
    public $price;
    public $status;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['autobrand_name', 'automodel_name', 'name', 'production_year', 'price', 'status'], 'required'],
            [['autobrand_name', 'automodel_name', 'name'], 'string'],
            [['autobrand_name', 'automodel_name', 'name'], 'filter', 'filter' => 'trim'],
            [['production_year', 'price'], 'integer'],
            [['status'], 'in', 'range' => Autocomp::status],
        ];
    }

    public function save()
    {
        $autocomp = new Autocomp();
        $autocomp->name = $this->name;
        $autocomp->production_year = $this->production_year;
        $autocomp->price = $this->price;
        $autocomp->status = $this->status;

        if (!$autobrand = Autobrand::find()->where(['name' => $this->autobrand_name])->one())
        {
            $autobrand = new Autobrand();
            $autobrand->name = $this->autobrand_name;
            $autobrand->save();
        }

        if (!$automodel = Automodel::find()->where(['name' => $this->automodel_name, 'autobrand_id' => $autobrand->id])->one())
        {
            $automodel = new Automodel();
            $automodel->name = $this->automodel_name;
            $automodel->autobrand_id = $autobrand->id;
            $automodel->save();
        }

        $autocomp->automodel_id = $automodel->id;
        $autocomp->save();

        return $autocomp;
    }

}