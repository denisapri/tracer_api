<?php
namespace app\models;
use Yii;
use app\widgets\AuthUser;
class KelompokUnitLayanan extends \yii\db\ActiveRecord
{
    static $prefix="kul";
    public static function tableName()
    {
        return 'pendaftaran_m_kelompok_unit_layanan';
    }
    public function rules()
    {
        return [
            [['kul_unit_id', 'kul_type', 'kul_tarif_tindakan_id'], 'required'],
            [['kul_unit_id', 'kul_type', 'kul_created_by', 'kul_updated_by', 'kul_deleted_by','kul_aktif'], 'integer'],
            [['kul_tarif_tindakan_id'], 'string'],
            [['kul_created_at', 'kul_updated_at', 'kul_deleted_at'], 'safe'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'kul_id' => 'Kul ID',
            'kul_unit_id' => 'Kul Unit ID',
            'kul_type' => 'Kul Type',
            'kul_aktif'=>'Kul Aktif',
            'kul_tarif_tindakan_id' => 'Kul Tarif Tindakan ID',
            'kul_created_at' => 'Kul Created At',
            'kul_created_by' => 'Kul Created By',
            'kul_updated_at' => 'Kul Updated At',
            'kul_updated_by' => 'Kul Updated By',
            'kul_deleted_at' => 'Kul Deleted At',
            'kul_deleted_by' => 'Kul Deleted By',
        ];
    }
    public static function find()
    {
        return new BaseQuery(get_called_class());
    }
    function getLayanan()
    {
        return $this->hasMany(Layanan::className(),['pl_unit_kode'=>'kul_unit_id']);
    }
    function getUnit()
    {
        return $this->hasOne(Unit::className(),['unt_id'=>'kul_unit_id']);
    }
    static function getListPoli()
    {
        $query= self::find()->joinWith(['unit'],false);
        if(AuthUser::isIgd()){
            $query->where(['kul_type'=>1]);
        }else{
            $query->where(['kul_type'=>2]);
        }
        return $query->select('kul_unit_id,kul_unit_id as kode,unt_nama as nama')->active(self::$prefix)->notDeleted(self::$prefix)->asArray()->all();
    }
    static function getRuangRawatinap()
    {
        return self::find()->joinWith(['unit'],false)->select('kul_unit_id,kul_unit_id as id,unt_nama as nama')->where(['kul_type'=>3])->notDeleted(self::$prefix)->orderBy(['unt_nama'=>SORT_ASC])->asArray()->all();
    }
    static function getTarifKonsultasi($unit)
    {
        $result=self::find()->select('kul_tarif_tindakan_id')->where(['kul_unit_id'=>$unit])->andWhere('kul_tarif_tindakan_id is not null')->notDeleted()->limit(1)->one();
        if($result!=NULL){
            $tmp=[];
            foreach($result->kul_tarif_tindakan_id as $t){
                $tmp[]=$t;
            }
            return $tmp;
        }
        return $result;
    }
}
