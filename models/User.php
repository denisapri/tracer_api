<?php
namespace app\models;

use app\rbac\models\Role;
use kartik\password\StrengthValidator;
use yii\behaviors\TimestampBehavior;
use Yii;
use app\helpers\BehaviorsFromParamsHelper;
use yii\rest\ActiveController;

/**
 * This is the user model class extending UserIdentity.
 * Here you can implement your custom user solutions.
 * 
 * @property Role[] $role
 * @property Article[] $articles
 */
class User extends UserIdentity
{
    // the list of status values that can be stored in user table
    const STATUS_ACTIVE   = 1;
    const STATUS_INACTIVE = 0; 
    static $prefix="pgw";

    /**
     * List of names for each status.
     * @var array
     */
	 
    public $statusList = [
        self::STATUS_ACTIVE   => 'Active',
        self::STATUS_INACTIVE => 'Inactive',
    ];

	
	public $updated_at;
    public $username;
    /**
     * Returns the validation rules for attributes.
     *
     * @return array
     */
    public function rules()
    {
        return [
            ['pgw_username', 'filter', 'filter' => 'trim'],
            ['pgw_username', 'required'],
            ['pgw_username', 'string', 'min' => 2, 'max' => 255],        
            ['pgw_username', 'unique'],
            [['consumer', 'access_given'], 'safe'],
            ['pgw_email', 'filter', 'filter' => 'trim'],
            ['pgw_email', 'required'],
            ['pgw_email', 'email'],
            ['pgw_email', 'string', 'max' => 255],
            ['pgw_aktif', 'required'],
        ];
    }

    /**
     * Returns a list of behaviors that this component should behave as.
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * Returns the attribute labels.
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'pgw_username' => Yii::t('app', 'Username'),
            'password' => Yii::t('app', 'Password'),
            'pgw_email' => Yii::t('app', 'Email'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

//------------------------------------------------------------------------------------------------//
// USER FINDERS
//------------------------------------------------------------------------------------------------//

    /**
     * Finds user by username.
     *
     * @param  string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        // return static::findOne(['pgw_username' => $username]);
       $data= self::find()->joinWith([
            'aksesaplikasi'=>function($q){
                $q->joinWith(['aplikasi'],false);
            }
        ],false)->where(['pgw_username' => $username])->andWhere(['apl_kode'=>Yii::$app->params['id']])->limit(1)->one();
        return $data;
    }  
    
    /**
     * Finds user by email.
     *
     * @param  string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['pgw_email' => $email]);
    } 

    /**
     * Finds user by password reset token.
     *
     * @param  string $token Password reset token.
     * @return null|static
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'pgw_password_reset_token' => $token,
            'pgw_aktif' => User::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by account activation token.
     *
     * @param  string $token Account activation token.
     * @return static|null
     */
   // public static function findByAccountActivationToken($token)
    //{
      //  return static::findOne([
        //    'pgw_account_activation_token' => $token,
         //   'pgw_aktif' => User::STATUS_INACTIVE,
        //]);
    //}

  
//------------------------------------------------------------------------------------------------//
// HELPERS
//------------------------------------------------------------------------------------------------//

    /**
     * Returns the user status in nice format.
     *
     * @param  integer $status Status integer value.
     * @return string          Nicely formatted status.
     */
    public function getStatusName($status)
    {
        return $this->statusList[$status];
    }

    /**
     * Generates new password reset token.
     */
    public function generatePasswordResetToken()
    {
        $this->pgw_password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }
    
    /**
     * Removes password reset token.
     */
    public function removePasswordResetToken()
    {
        $this->pgw_password_reset_token = null;
    }

    /**
     * Finds out if password reset token is valid.
     * 
     * @param  string $token Password reset token.
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * Generates new account activation token.
     */
    //public function generateAccountActivationToken()
    //{
      //  $this->pgw_account_activation_token = Yii::$app->security->generateRandomString() . '_' . time();
    //}

    /**
     * Removes account activation token.
     */
   // public function removeAccountActivationToken()
    //{
      //  $this->account_activation_token = null;
    //}
    
    function getAksesaplikasi()
    {
        return $this->hasMany(AksesAplikasi::className(),['akp_pgw_id'=>'pgw_id']);
    }
}
