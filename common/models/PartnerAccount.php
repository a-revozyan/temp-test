<?php

namespace common\models;

use common\helpers\DateHelper;
use Yii;

/**
 * This is the model class for table "partner_account".
 *
 * @property int $id
 * @property int|null $partner_id
 * @property int|null $amount
 * @property int|null $user_id
 * @property string|null $note
 * @property string|null $created_at
 *
 * @property Partner|null $partner
 * @property \backapi\models\User|null $user
 */
class PartnerAccount extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'partner_account';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['partner_id', 'amount', 'user_id'], 'default', 'value' => null],
            [['partner_id', 'amount', 'user_id'], 'integer'],
            [['created_at'], 'safe'],
            [['note'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'partner_id' => 'Partner ID',
            'amount' => 'Amount',
            'note' => 'Note',
            'created_at' => 'Created At',
        ];
    }

    public function getPartner()
    {
        return $this->hasOne(Partner::className(), ['id' => 'partner_id']);
    }

    public function getUser()
    {
        return $this->hasOne(\backapi\models\User::className(), ['id' => 'user_id']);
    }

    public static function getShortArrCollection($models)
    {
        $_models = [];
        foreach ($models as $model) {
            $_models[] = $model->getShortArr();
        }

        return $_models;
    }

    public function getShortArr()
    {
        return [
            'id' => $this->id,
            'partner' => $this->partner->getForIdNameArr(),
            'amount' => $this->amount,
            'note' => $this->note,
            'user' => $this->user->getShortArr(),
            'created_at' => !empty($this->created_at) ? DateHelper::date_format($this->created_at, 'Y-m-d H:i:s', 'd.m.Y H:i:s') : null,
        ];
    }
}
