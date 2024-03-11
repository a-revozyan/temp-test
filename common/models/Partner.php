<?php

namespace common\models;

use common\helpers\GeneralHelper;
use Yii;

/**
 * This is the model class for table "partner".
 *
 * @property int $id
 * @property string $name
 * @property string $image
 * @property int $status
 * @property int|null $travel_currency_id
 * @property int $created_at
 * @property int $updated_at
 * @property string $contract_number
 * @property integer $f_user_id
 * @property integer $service_amount
 * @property string|null $hook_url
 * @property integer|null $accident_type_id
 * @property string|null $travel_offer_file
 * @property User $fUser
 * @property AccidentType $accidentType
 * @property \backapi\models\User $user
 *
 * @property PartnerProduct[] $partnerProducts
 */
class Partner extends \yii\db\ActiveRecord
{
    public $available_car_inspection_count;
    public $done_car_inspection_count;
    public $partner_accounts_count;

    public const PARTNER_ROLE_NAME = "partner";

    public $imageFile;
    public const PARTNER = [
        'gross' => 1,
        'kapital' => 18,
        'neo' => 22,
        'insonline' => 23,
    ];

    public const STATUS = [
        'active' => 1,
        'inactive' => 0,
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'partner';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'image', 'created_at', 'updated_at'], 'required'],
            [['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg', 'on' => 'insert'],
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg', 'on' => 'update'],
            [['status', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['status', 'travel_currency_id', 'created_at', 'updated_at', 'f_user_id', 'service_amount', 'accident_type_id'], 'integer'],
            [['name', 'image', 'hook_url', 'travel_offer_file'], 'string', 'max' => 255],
            [['travel_currency_id'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::className(), 'targetAttribute' => ['travel_currency_id' => 'id']],
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $this->imageFile->saveAs('../../frontend/web/uploads/partners/' . $this->image);
            return true;
        } else {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Partner Name'),
            'image' => Yii::t('app', 'Image'),
            'status' => Yii::t('app', 'Status'),
            'contract_number' => Yii::t('app', 'Contract number'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[PartnerProducts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPartnerProducts()
    {
        return $this->hasMany(PartnerProduct::className(), ['partner_id' => 'id']);
    }

    public function getProducts()
    {
        return $this->hasMany(Product::className(), ['id' => 'product_id'])->via('partnerProducts');
    }

    public function getCurrency()
    {
        return $this->hasOne(Currency::className(), ['id' => 'travel_currency_id']);
    }

    public function getAccidentType()
    {
        return $this->hasOne(AccidentType::className(), ['id' => 'accident_type_id']);
    }

    public function getAutocomp()
    {
        return $this->hasMany(Autocomp::className(), ['id' => 'autocomp_id'])
            ->viaTable('autocomp_partner', ['partner_id' => 'id']);
    }

    public function getFUser()
    {
        return $this->hasOne(User::className(), ['id' => 'f_user_id']);
    }

    public function getUser()
    {
        return $this->hasOne(\backapi\models\User::className(), ['partner_id' => 'id']);
    }

    public static function getShortArrCollection($partners)
    {
        $_partners = [];
        foreach ($partners as $partner) {
            $_partners[] = $partner->getShortArr();
        }
        return $_partners;
    }

    public static function getForIdNameArrCollection($models)
    {
        $_models = [];
        foreach ($models as $model) {
            $_models[$model->id] = $model->getForIdNameArr();
        }
        return $_models;
    }

    public function getShortArr()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->fUser->phone ?? null,
            'contract_number' => $this->contract_number,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'status' => $this->status,
            'hook_url' => $this->hook_url,
            'service_amount' => $this->service_amount,
            'image' => GeneralHelper::env('frontend_project_website') . '/uploads/partners/' . $this->image,
            'travel_offer_file' => !empty($this->travel_offer_file) ? GeneralHelper::env('frontend_project_website') . '/uploads/partners/travel_offer_file/' . $this->travel_offer_file : null,
        ];
    }

    public function getForIdNameArr()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => !empty($this->image) ? GeneralHelper::env('frontend_project_website') . '/uploads/partners/' . $this->image : null,
            'travel_offer_file' => !empty($this->travel_offer_file) ? GeneralHelper::env('frontend_project_website') . '/uploads/partners/travel_offer_file/' . $this->travel_offer_file : null,
        ];
    }

    public function getForIdNameAccidentArr()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => !empty($this->image) ? GeneralHelper::env('frontend_project_website') . '/uploads/partners/' . $this->image : null,
            'accident' => !empty($this->accidentType) ? $this->accidentType->getShortArr() : null
        ];
    }

    public static function getCarInspectionArrCollection($models)
    {
        $_models = [];
        foreach ($models as $model) {
            $_models[] = $model->getCarInspectionArr();
        }
        return $_models;
    }

    public function getCarInspectionArr()
    {
        return [
            'id' => $this->id,
            'username' => $this->user->username ?? null,
            'name' => $this->name,
            'service_amount' => $this->service_amount,
            'available_car_inspection_count' => $this->available_car_inspection_count,
            'done_car_inspection_count' => $this->done_car_inspection_count,
            'status' => $this->status,
            'partner_accounts_count' => $this->partner_accounts_count,
        ];
    }

}
