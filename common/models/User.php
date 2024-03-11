<?php
namespace common\models;

use common\helpers\DateHelper;
use common\helpers\GeneralHelper;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $phone
 * @property string $password_hash
 * @property string $email
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 * @property integer $time_of_sending_sms
 * @property integer $big_time_of_sending_sms
 * @property string $first_name
 * @property string $last_name
 * @property string $bridge_company_code
 * @property integer $sent_sms_count
 * @property integer $role
 * @property integer $bridge_company_id
 * @property integer $city_id
 * @property integer $gender
 * @property string $birthday
 * @property string $passport_seria
 * @property string $passport_number
 *
 * @property Token[] $tokens
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;

    const ROLES = [
        'user' => 0,
        'agent' => 1,
        'developer' => 2,
        'partner' => 3,
        'partner_client' => 4,
    ];

    const GENDER = [
        'female' => 0,
        'male' => 1,
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%f_user}}';
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), ['products']);
    }

    public static function getSmsFilteredUsersQuery($attributes)
    {
        $query = Product::getProductsQuery()->filterWhere([
            'and',
            ['in', 'LEFT(autonumber, 2)', $attributes['region_car_numbers']],
            ['number_drivers_id' => $attributes['number_drivers_id']],
            ['>', 'policy_generated_date', !empty($attributes['bought_from_date']) ? date_create_from_format('Y-m-d H:i:s', $attributes['bought_from_date'])->getTimestamp() : null],
            ['<', 'policy_generated_date', !empty($attributes['bought_till_date']) ? date_create_from_format('Y-m-d H:i:s', $attributes['bought_till_date'])->getTimestamp() : null],
            ['>', 'f_user.created_at', !empty($attributes['registered_from_date']) ? date_create_from_format('Y-m-d H:i:s', $attributes['registered_from_date'])->getTimestamp() : null],
            ['<', 'f_user.created_at', !empty($attributes['registered_till_date']) ?  date_create_from_format('Y-m-d H:i:s', $attributes['registered_till_date'])->getTimestamp() : null],
            ['products.product' => !empty($attributes['product']) ? $attributes['product'] : Product::products]
        ])
            ->leftJoin('f_user', 'products.f_user_id = f_user.id')
            ->groupBy('products.f_user_id')
            ->select('products.f_user_id as f_user_id')
            ->leftJoin(
                [
                    "telegram_chat_ids_count" => Token::find()->select([
                        "coalesce(count(token.id), 0) as count",
                        'token.f_user_id',
                    ])
                        ->where(['not', ['telegram_chat_id' => null]])
                        ->groupBy('token.f_user_id')
                ],
                '"telegram_chat_ids_count"."f_user_id" = "products"."f_user_id"'
            );

        if ($attributes['type'] == SmsTemplate::TYPE['users_which_have_telegram_via_telegram'])
            $query->andWhere(['not', ['telegram_chat_ids_count.count' => 0]]);
        elseif ($attributes['type'] == SmsTemplate::TYPE['users_which_have_not_telegram_via_sms'])
            $query->andWhere(['telegram_chat_ids_count.count' => 0]);

        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sent_sms_count', 'big_time_of_sending_sms', 'time_of_sending_sms', 'birthday', 'passport_seria', 'passport_number'], 'safe'],
            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            [['bridge_company_id', 'city_id', 'gender'], 'integer'],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return Token::findOne(['access_token' => $token])->fUser ?? null;
    }

    /**
     * Finds user by phone
     *
     * @param string $phone
     * @return static|null
     */
    public static function findByPhone($phone)
    {
        return static::findOne(['phone' => $phone]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
//    public static function findByPasswordResetToken($token)
//    {
//        if (!static::isPasswordResetTokenValid($token)) {
//            return null;
//        }
//
//        return static::findOne([
//            'password_reset_token' => $token,
//            'status' => self::STATUS_ACTIVE,
//        ]);
//    }

    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     * @return static|null
     */
//    public static function findByVerificationToken($token) {
//        return static::findOne([
//            'verification_token' => $token,
//            'status' => self::STATUS_INACTIVE
//        ]);
//    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
//    public static function isPasswordResetTokenValid($token)
//    {
//        if (empty($token)) {
//            return false;
//        }
//
//        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
//        $expire = GeneralHelper::env('user.passwordResetTokenExpire');
//        return $timestamp + $expire >= time();
//    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
//        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
//        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
//    public function generateAuthKey()
//    {
//        $this->auth_key = Yii::$app->security->generateRandomString();
//    }

    /**
     * Generates new password reset token
     */
//    public function generatePasswordResetToken()
//    {
//        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
//    }

    /**
     * Generates new token for email verification
     */
//    public function generateEmailVerificationToken()
//    {
//        $this->verification_token = mt_rand(10000, 99999);
//    }

    /**
     * Removes password reset token
     */
//    public function removePasswordResetToken()
//    {
//        $this->password_reset_token = null;
//    }

    public function getBridgeCompany()
    {
        return $this->hasOne(BridgeCompany::className(), ['id' => 'bridge_company_id']);
    }

    public function getAgent()
    {
        return $this->hasOne(Agent::className(), ['f_user_id' => 'id']);
    }

    public function getPartner()
    {
        return $this->hasOne(Partner::className(), ['f_user_id' => 'id']);
    }

    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }

    public function getTokens()
    {
        return $this->hasMany(Token::className(), ['f_user_id' => 'id']);
    }

    public function telegram_chat_ids()
    {
        return $this->getTokens()
            ->andWhere(['not', ['telegram_chat_id' => null]])
            ->select(['telegram_chat_id'])->column();
    }

    public function getArrForAgent()
    {
        return [
            'id' => $this->id,
            'phone' => $this->phone,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
        ];
    }

    public function getShortArr()
    {
        return [
            'id' => $this->id,
            'phone' => $this->phone,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'role' => $this->role,
        ];
    }

    public function getFullArr()
    {
        return [
            'id' => $this->id,
            'phone' => $this->phone,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'role' => $this->role,
            'gender' => $this->gender,
            'birthday' => !empty($this->birthday) ? DateHelper::date_format($this->birthday, 'Y-m-d', 'd.m.Y') : null,
            'passport_seria' => $this->passport_seria,
            'passport_number' => $this->passport_number,
            'city' => !empty($this->city_id) ? $this->city->getShortArr() : null,
        ];
    }

}
