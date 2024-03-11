<?php

namespace backapi\models;

use common\models\BridgeCompany;
use common\models\Kasko;
use common\models\Partner;
use common\models\Region;
use common\models\Token;
use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

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
 * @property string|null $phone_number
 * @property string|null $last_name
 * @property string|null $first_name
 * @property int|null $region_id
 * @property string|null $access_token
 *
 * @property Kasko[] $kaskos
 * @property Partner $partner
 * @property Region $region
 */
class User extends \mdm\admin\models\User implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'auth_key', 'password_hash', 'email', 'created_at', 'updated_at'], 'required'],
            [['status', 'created_at', 'updated_at', 'partner_id', 'region_id'], 'default', 'value' => null],
            [['status', 'created_at', 'updated_at', 'partner_id', 'region_id'], 'integer'],
            [['username', 'auth_key'], 'string', 'max' => 32],
            [['password_hash', 'password_reset_token', 'email', 'verification_token', 'phone_number', 'last_name', 'first_name', 'access_token'], 'string', 'max' => 255],
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
            'password_hash' => Yii::t('app', 'Password Hash'),
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
            'access_token' => Yii::t('app', 'Access Token'),
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

    public function getBridgeCompany()
    {
        return $this->hasOne(BridgeCompany::className(), ['user_id' => 'id']);
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

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return Token::findOne(['access_token' => $token])->user ?? null;
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function getShortArr()
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'name' => $this->last_name . " " . $this->first_name,
            'phone_number' => $this->phone_number,
        ];
    }
}
