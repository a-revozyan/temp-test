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


class AutocompAttachPartnerForm extends Model
{
    public $autocomp_id;
    public $partner_ids;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['autocomp_id'], 'required'],
            [['autocomp_id'], 'integer'],
            [['autocomp_id'], 'exist', 'skipOnError' => true, 'targetClass' => Autocomp::className(), 'targetAttribute' => ['autocomp_id' => 'id']],
            [['partner_ids'], 'default', 'value' => [], 'skipOnEmpty' => false],
            [['partner_ids'], 'each', 'rule' => ['integer']],
            [['partner_ids'], 'each', 'rule' => ['exist', 'skipOnError' => true, 'targetClass' => Partner::className(), 'targetAttribute' => ['partner_ids' => 'id']]],
        ];
    }

    public function save()
    {
        $autocomp = Autocomp::findOne($this->autocomp_id);
        $autocomp->unlinkAll('partners', true);
        foreach ($this->partner_ids as $partner_id) {
            $partner = Partner::findOne($partner_id);
            $autocomp->link('partners', $partner);
        }

        return $autocomp;
    }

}