<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "bridge_company".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $code
 * @property int $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $user_id
 * @property string|null $success_webhook_url
 * @property string|null $error_webhook_url
 * @property string|null $authorization
 *
 * @property Kasko[] $kaskos
 * @property \mdm\admin\models\User $user
 */
class BridgeCompany extends \yii\db\ActiveRecord
{
    public const BRIDGE_COMPANY_ROLE_NAME = "bridge_company";
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bridge_company';
    }

    public const STATUS = [
        'inactive' => 0,
        'active' => 1,
    ];

    public const BRIDGE_COMPANY = [
        'road24' => 16,
        'paynet' => 17,
    ];

    public const TelegramChatId = [
        16 => -1002092587291
    ];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at', 'user_id'], 'default', 'value' => null],
            [['created_at', 'updated_at'], 'integer'],
            [['name', 'code'], 'string', 'max' => 255],
            [['name', 'code', 'status'], 'required'],
            [['code'], 'unique'],
            [['error_webhook_url', 'success_webhook_url', 'authorization'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'code' => Yii::t('app', 'Code'),
            'status' => Yii::t('app', 'Status'),
            'user_id' => Yii::t('app', 'User Id'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[Kaskos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKaskos()
    {
        return $this->hasMany(Kasko::className(), ['bridge_company_id' => 'id']);
    }

    public function getMonthlyDivvies()
    {
        return $this->hasMany(PartnerMonthBridgeCompanyDivvy::className(), ['bridge_company_id' => 'id']);
    }

    public function getUser()
    {
        return $this->hasOne(\mdm\admin\models\User::className(), ['id' => 'user_id']);
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
            'name' => $this->name,
            'code' => $this->code,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'status' => $this->status,
            'username' => !is_null($this->user) ? $this->user->username : null,
            'phone_number' => !is_null($this->user) ? $this->user->phone_number : null,
            'email' => !is_null($this->user) ? $this->user->email : null,
            'first_name' => !is_null($this->user) ? $this->user->first_name : null,
            'last_name' => !is_null($this->user) ? $this->user->last_name : null,
        ];
    }

    public function getShortWithDivvyArr()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'status' => $this->status,
            'username' => !is_null($this->user) ? $this->user->username : null,
            'phone_number' => !is_null($this->user) ? $this->user->phone_number : null,
            'email' => !is_null($this->user) ? $this->user->email : null,
            'first_name' => !is_null($this->user) ? $this->user->first_name : null,
            'last_name' => !is_null($this->user) ? $this->user->last_name : null,
            'success_webhook_url' => $this->success_webhook_url,
            'error_webhook_url' => $this->error_webhook_url,
            'divvies' => !empty($this->monthlyDivvies) ? PartnerMonthBridgeCompanyDivvy::getShortArrCollection($this->monthlyDivvies) : [],
        ];
    }

    public function getIdNameArr()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
