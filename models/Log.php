<?php
namespace app\models;
use Yii;
use app\widgets\AuthUser;
class Log extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'log';
    }
    public function rules()
    {
        return [
            [['log_user_id', 'log_user_ip', 'log_action'], 'required'],
            [['log_user_id'], 'default', 'value' => null],
            [['log_user_id'], 'integer'],
            [['log_data'], 'string'],
            [['log_created_at'], 'safe'],
            [['log_user_ip'], 'string', 'max' => 15],
            [['log_action', 'log_media'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels()
    {
        return [
            'log_id' => 'Log ID',
            'log_user_id' => 'Log User ID',
            'log_user_ip' => 'Log User Ip',
            'log_action' => 'Log Action',
            'log_data' => 'Log Data',
            'log_media' => 'Log Media',
            'log_created_at' => 'Log Created At',
        ];
    }
    static function saveLog($action,$data=[])
    {
        Yii::$app->db->createCommand()->insert(self::tableName(),[
            'log_user_id'=>Yii::$app->user->identity->id,
            'log_user_ip'=>Yii::$app->request->userIp,
            'log_action'=>$action,
            'log_data'=>(count($data)>0) ? json_encode($data) : null,
            'log_media'=>Yii::$app->request->getUserAgent(),
            'log_created_at'=>date('Y-m-d H:i:s'),
         ])->execute();
    }
    static function getByUser()
    {
        return self::find()->select(['log_action as action','log_created_at as date'])->where(['log_user_id'=>AuthUser::user()->id])->orderBy(['log_created_at'=>SORT_DESC])->limit(10)->asArray()->all();
    }
}