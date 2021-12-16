<?php
namespace app\models;
use Yii;
class Pangkat extends \yii\db\ActiveRecord
{
    static $prefix="smp";
    public static function tableName()
    {
        return 'sdm_m_pangkat';
    }
    public function rules()
    {
        return [
            [['smp_nama', 'smp_aktif'], 'required'],
            [['smp_aktif', 'smp_created_by', 'smp_updated_by', 'smp_deleted_by'], 'integer'],
            [['smp_created_at', 'smp_updated_at', 'smp_deleted_at'], 'safe'],
            [['smp_nama'], 'string', 'max' => 100],
        ];
    }
    public function attributeLabels()
    {
        return [
            'smp_id' => 'Smp ID',
            'smp_nama' => 'Smp Nama',
            'smp_aktif' => 'Smp Aktif',
            'smp_created_at' => 'Smp Created At',
            'smp_created_by' => 'Smp Created By',
            'smp_updated_at' => 'Smp Updated At',
            'smp_updated_by' => 'Smp Updated By',
            'smp_deleted_at' => 'Smp Deleted At',
            'smp_deleted_by' => 'Smp Deleted By',
        ];
    }
    public static function find()
    {
        return new BaseQuery(get_called_class());
    }
    function getDetail()
    {
        return $this->hasMany(PangkatDetail::className(),['smpd_smp_id'=>'smp_id']);
    }
    static function all()
    {
        return self::find()->select('smp_id,smp_nama')->with(['detail'=>function($q){ $q->select('smpd_id,smpd_smp_id,smpd_nama'); }])->orderBy(['smp_nama'=>SORT_ASC])->active(self::$prefix)->notDeleted(self::$prefix)->asArray()->all();
    }
}
