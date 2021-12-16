<?php
namespace app\models;
use Yii;
use app\widgets\AuthUser;
class AksesAplikasi extends \yii\db\ActiveRecord
{
    static $prefix="akp";
    public static function tableName()
    {
        return 'sdm_m_akses_aplikasi';
    }
    public function rules()
    {
        return [
            [['akp_pgw_id', 'akp_apl_id', 'akp_created_by'], 'required'],
            [['akp_pgw_id', 'akp_apl_id', 'akp_all_id', 'akp_aktif', 'akp_created_by', 'akp_updated_by', 'akp_deleted_by'], 'integer'],
            [['akp_created_at', 'akp_updated_at', 'akp_deleted_at'], 'safe'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'akp_id' => 'Akp ID',
            'akp_pgw_id' => 'Akp Pgw ID',
            'akp_apl_id' => 'Akp Apl ID',
            'akp_all_id' => 'Akp All ID',
            'akp_aktif' => 'Akp Aktif',
            'akp_created_at' => 'Akp Created At',
            'akp_created_by' => 'Akp Created By',
            'akp_updated_at' => 'Akp Updated At',
            'akp_updated_by' => 'Akp Updated By',
            'akp_deleted_at' => 'Akp Deleted At',
            'akp_deleted_by' => 'Akp Deleted By',
        ];
    }
    public static function find()
    {
        return new BaseQuery(get_called_class());
    }
    function getAplikasi()
    {
        return $this->hasOne(Aplikasi::className(),['apl_id'=>'akp_apl_id']);
    }
    function getAplikasilevel()
    {
        return $this->hasOne(AplikasiLevel::className(),['all_id'=>'akp_all_id']);
    }
    static function userAkses()
    {
        $data = self::find()->joinWith(['aplikasi','aplikasilevel'],false)->select(['all_nama'])->where(['akp_pgw_id'=>AuthUser::user()->id,'apl_kode'=>Yii::$app->params['id']])->active(self::$prefix)->notDeleted(self::$prefix)->asArray()->all();
        if(count($data)>0){
            return array_column($data,'all_nama');
        }
        return NULL;
    }
}