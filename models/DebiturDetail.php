<?php
namespace app\models;
use Yii;
class DebiturDetail extends \yii\db\ActiveRecord
{
    static $prefix="pmdd";
    public static function tableName()
    {
        return 'pendaftaran_m_debitur_detail';
    }
    public function rules()
    {
        return [
            [['pmdd_kode', 'pmdd_pmd_kode', 'pmdd_nama', 'pmdd_aktif', 'pmdd_created_by'], 'required'],
            [['pmdd_aktif', 'pmdd_created_by', 'pmdd_updated_by', 'pmdd_deleted_by'], 'integer'],
            [['pmdd_created_at', 'pmdd_updated_at', 'pmdd_deleted_at'], 'safe'],
            [['pmdd_kode', 'pmdd_pmd_kode'], 'string', 'max' => 10],
            [['pmdd_nama'], 'string', 'max' => 255],
            [['pmdd_kode'], 'unique'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'pmdd_kode' => 'Pmdd Kode',
            'pmdd_pmd_kode' => 'Pmd Kode',
            'pmdd_nama' => 'Pmdd Nama',
            'pmdd_aktif' => 'Pmdd Aktif',
            'pmdd_created_at' => 'Pmdd Created At',
            'pmdd_created_by' => 'Pmdd Created By',
            'pmdd_updated_at' => 'Pmdd Updated At',
            'pmdd_updated_by' => 'Pmdd Updated By',
            'pmdd_deleted_at' => 'Pmdd Deleted At',
            'pmdd_deleted_by' => 'Pmdd Deleted By',
        ];
    }
    public static function find()
    {
        return new BaseQuery(get_called_class());
    }
    function getDebitur()
    {
        return $this->hasOne(Debitur::className(),['pmd_kode'=>'pmdd_pmd_kode']);
    }
    static function all($kode=NULL)
    {
        $query= self::find();
        if($kode!=NULL){
            if(is_array($kode)){
                $query->where(['in','pmdd_kode',$kode]);
            }else{
                $query->where(['pmdd_pmd_kode'=>$kode]);
            }
        }else{
            $query->where(['not in','pmdd_kode',['1012']]);
        }
        return $query->select('pmdd_kode as kode,pmdd_nama as nama,pmdd_pmd_kode as debitur')->active(self::$prefix)->notDeleted(self::$prefix)->asArray()->all();
    }
}
