<?php
namespace app\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\base\NotSupportedException;
use Yii;

/**
 * UserIdentity class for "user" table.
 * This is a base user class that is implementing IdentityInterface.
 * User model should extend from this model, and other user related models should
 * extend from User model.
 *
* @property integer $id
 * @property string  $pgw_username
 * @property string  $password_hash
 * @property string  $password_reset_token
 * @property string  $email
 * @property string  $consumer
 * @property string  $access_given
 * @property string  $account_activation_token
 * @property string  $pgw_auth_key
 * @property integer $pgw_aktif
 * @property integer $pgw_created_at
 * @property integer $pgw_updated_at
 */
class UserIdentity extends ActiveRecord implements IdentityInterface
{
    public $consumer;
    /**
     * Declares the name of the database table associated with this AR class.
     *
     * @return string
     */
    public static function tableName()
    {
        return '{{%sdm_m_pegawai}}';
    }

//------------------------------------------------------------------------------------------------//
// IDENTITY INTERFACE IMPLEMENTATION
//------------------------------------------------------------------------------------------------//

    /**
     * Finds an identity by the given ID.
     *
     * @param  int|string $id The user id.
     * @return IdentityInterface|static
     */
    public static function findIdentity($id)
    {
        // return static::findOne(['pgw_id' => $id, 'pgw_aktif' => User::STATUS_ACTIVE]);
        
         return self::find()->joinWith([
            'aksesaplikasi'=>function($q){
                $q->joinWith(['aplikasi'],false);
            }
        ],false)->where(['pgw_id'=>$id,'apl_kode'=>Yii::$app->params['id']])->limit(1)->one();
    }

    /**
     * Finds an identity by the given access token.
     *
     * @param  mixed $token
     * @param  null  $type
     * @return void|IdentityInterface
     * 
     * @throws NotSupportedException
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $accessToken = AccessToken::find()->where(['token' => $token])->andWhere(['>', 'expire_at', strtotime('now')])->one();
        if(!$accessToken) return $accessToken;
        return User::findOne(['pgw_id' => $accessToken->user_id]);
        // return User::findOne(['auth_key' => $token, 'status' => User::STATUS_ACTIVE]);
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     *
     * @return int|mixed|string
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * Returns a key that can be used to check the validity of a given
     * identity ID. The key should be unique for each individual user, and
     * should be persistent so that it can be used to check the validity of
     * the user identity. The space of such keys should be big enough to defeat
     * potential identity attacks.
     *
     * @return string
     */
    public function getAuthKey()
    {
        return $this->pgw_auth_key;
    }

    /**
     * Validates the given auth key.
     * 
     * @param  string  $authKey The given auth key.
     * @return boolean          Whether the given auth key is valid.
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

//------------------------------------------------------------------------------------------------//
// IMPORTANT IDENTITY HELPERS
//------------------------------------------------------------------------------------------------//

    /**
     * Generates "remember me" authentication key. 
     */
    public function generateAuthKey()
    {
        $this->pgw_auth_key = Yii::$app->security->generateRandomString();
        AccessToken::generateAuthKey($this);
    }

    /**
     * Validates password.
     *
     * @param  string $password
     * @return bool
     * 
     * @throws \yii\base\InvalidConfigException
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->pgw_password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model.
     *
     * @param  string $password
     * 
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function setPassword($password)
    {
        $this->pgw_password_hash = Yii::$app->security->generatePasswordHash($password);
    }

}
