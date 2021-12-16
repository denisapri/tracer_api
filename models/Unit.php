<?php
namespace app\models;
use Yii;
class Unit extends \yii\db\ActiveRecord
{
    static $prefix="unt";
    public static function tableName()
    {
        return 'sdm_m_unit';
    }
    public function rules()
    {
        return [
            [['unt_rumpun_id', 'unt_nama'], 'required'],
            [['unt_rumpun_id', 'unt_aktif', 'unt_created_by', 'unt_updated_by', 'unt_deleted_by'], 'integer'],
            [['unt_created_at', 'unt_updated_at', 'unt_deleted_at'], 'safe'],
            [['unt_nama'], 'string', 'max' => 100],
        ];
    }
    public function attributeLabels()
    {
        return [
            'unt_id' => 'Unt ID',
            'unt_rumpun_id' => 'Unt Rumpun ID',
            'unt_nama' => 'Unt Nama',
            'unt_aktif' => 'Unt Aktif',
            'unt_created_at' => 'Unt Created At',
            'unt_created_by' => 'Unt Created By',
            'unt_updated_at' => 'Unt Updated At',
            'unt_updated_by' => 'Unt Updated By',
            'unt_deleted_at' => 'Unt Deleted At',
            'unt_deleted_by' => 'Unt Deleted By',
        ];
    }
    public static function find()
    {
        return new BaseQuery(get_called_class());
    }
    function getLayanan()
    {
        return $this->hasMany(Layanan::className(),['pl_unit_kode'=>'unt_id']);
    }
    function getKelompokunit()
    {
        return $this->hasMany(KelompokUnitLayanan::className(),['kul_unit_id'=>'unt_id']);
    }
    static function all($kode=NULL)
    {
        $query= self::find();
        if($kode!=NULL){
            if(is_array($kode)){
                $query->where(['in','unt_id',$kode]);
            }else{
                $query->where(['unt_id'=>$kode]);
            }
        }
        return $query->select('unt_id as id,unt_nama as unit')->active(self::$prefix)->notDeleted(self::$prefix)->asArray()->all();
    }
}
