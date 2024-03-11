<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string|null $password_reset_token
 * @property string $email
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property string|null $verification_token
 * @property int|null $partner_id
 * @property string|null $number
 * @property string|null $phone_number
 * @property string|null $last_name
 * @property string|null $first_name
 * @property int|null $region_id
 *
 * @property Kasko[] $kaskos
 * @property Partner $partner
 * @property Region $region
 * @property Region $access_token
 * @property integer $service_amount
 */
class Surveyer extends \yii\db\ActiveRecord
{
    public const SURVEYER_ROLE_NAME = "surveyer";
    public const STATUSE_LABELS = [
          0 => 'InActive',
          10 => 'Active',
    ];

    public $password;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    public function fields()
    {
        $fields = parent::fields();
        $fields['created_at']  = function ($model) {
            if ($model->created_at == null)
                return null;
            return date('d.m.Y H:i', $model->created_at);
        };

        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'auth_key', 'password_hash', 'email', 'created_at', 'updated_at', 'phone_number'], 'required'],
            [['status', 'created_at', 'updated_at', 'partner_id', 'region_id'], 'default', 'value' => null],
            [['status', 'created_at', 'updated_at', 'partner_id', 'region_id'], 'integer'],
            [['username', 'auth_key'], 'string', 'max' => 32],
            [['password_hash', 'password_reset_token', 'email', 'verification_token', 'phone_number', 'last_name', 'first_name'], 'string', 'max' => 255],
            [['username', 'phone_number'], 'unique'],
            [['partner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Partner::className(), 'targetAttribute' => ['partner_id' => 'id']],
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Region::className(), 'targetAttribute' => ['region_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'username' => Yii::t('app', 'Username'),
            'auth_key' => Yii::t('app', 'Auth Key'),
            'password_hash' => Yii::t('app', 'Password'),
            'password_reset_token' => Yii::t('app', 'Password Reset Token'),
            'email' => Yii::t('app', 'Email'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'verification_token' => Yii::t('app', 'Verification Token'),
            'partner_id' => Yii::t('app', 'Partner ID'),
            'phone_number' => Yii::t('app', 'Phone Number'),
            'last_name' => Yii::t('app', 'Last Name'),
            'first_name' => Yii::t('app', 'First Name'),
            'region_id' => Yii::t('app', 'Region ID'),
        ];
    }

    /**
     * Gets query for [[Kaskos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKaskos()
    {
        return $this->hasMany(Kasko::className(), ['surveyer_id' => 'id']);
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
     * Gets query for [[Region]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Region::className(), ['id' => 'region_id']);
    }

    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function getShortArr()
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone_number' => $this->phone_number,
            'status' => $this->status,
        ];
    }
}
