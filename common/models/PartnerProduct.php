<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "partner_product".
 *
 * @property int $id
 * @property int $partner_id
 * @property int $product_id
 * @property float $percent
 * @property int $star
 * @property string $public_offer_ru
 * @property string $public_offer_uz
 * @property string $public_offer_en
 * @property string $conditions_ru
 * @property string $conditions_uz
 * @property string $conditions_en
 * @property string $delivery_info_ru
 * @property string $delivery_info_uz
 * @property string $delivery_info_en
 *
 * @property Partner $partner
 * @property Product $product
 */
class PartnerProduct extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public $public_offer_ruFile;
    public $public_offer_uzFile;
    public $public_offer_enFile;
    public $conditions_ruFile;
    public $conditions_uzFile;
    public $conditions_enFile;
    public static function tableName()
    {
        return 'partner_product';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['partner_id', 'product_id'], 'required'],
            [['public_offer_ruFile', 'public_offer_uzFile', 'public_offer_enFile', 'conditions_ruFile', 'conditions_uzFile', 'conditions_enFile'], 'file'],
            [['partner_id', 'product_id'], 'default', 'value' => null],
            [['partner_id', 'product_id', 'star'], 'integer'],
            [['percent'], 'integer'],
            [['delivery_info_ru', 'delivery_info_uz', 'delivery_info_en'], 'string', 'max' => 255],
            [['partner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Partner::className(), 'targetAttribute' => ['partner_id' => 'id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'id']],
        ];
    }

     public function uploadPublicOfferRu()
    {
        if ($this->validate()) {
            $this->public_offer_ruFile->saveAs('../../frontend/web/uploads/offer/' . $this->public_offer_ru);
            return true;
        } else {
            return false;
        }
    }

    public function uploadPublicOfferUz()
    {
        if ($this->validate()) {
            $this->public_offer_uzFile->saveAs('../../frontend/web/uploads/offer/' . $this->public_offer_uz);
            return true;
        } else {
            return false;
        }
    }

    public function uploadPublicOfferEn()
    {
        if ($this->validate()) {
            $this->public_offer_enFile->saveAs('../../frontend/web/uploads/offer/' . $this->public_offer_en);
            return true;
        } else {
            return false;
        }
    }

    public function uploadConditionsRu()
    {
        if ($this->validate()) {
            $this->conditions_ruFile->saveAs('../../frontend/web/uploads/conditions/' . $this->conditions_ru);
            return true;
        } else {
            return false;
        }
    }

    public function uploadConditionsUz()
    {
        if ($this->validate()) {
            $this->conditions_uzFile->saveAs('../../frontend/web/uploads/conditions/' . $this->conditions_uz);
            return true;
        } else {
            return false;
        }
    }

    public function uploadConditionsEn()
    {
        if ($this->validate()) {
            $this->conditions_enFile->saveAs('../../frontend/web/uploads/conditions/' . $this->conditions_en);
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
            'partner_id' => Yii::t('app', 'Partner ID'),
            'product_id' => Yii::t('app', 'Product ID'),
            'percent' => Yii::t('app', 'Percent'),
        ];
    }

    /**
     * Gets query for [[Partner]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPartner()
    {
        return $this->hasOne(Partner::className(), ['id' => 'partner_id']);
    }

    /**
     * Gets query for [[Product]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }
}
