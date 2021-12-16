<?php
namespace app\models;
use Yii;
class PangkatDetail extends \yii\db\ActiveRecord
{
    static $prefix="smpd";
    public static function tableName()
    {
        return 'sdm_m_pangkat_detail';
    }
    public function rules()
    {
        return [
            [['smpd_smp_id', 'smpd_nama', 'smpd_aktif'], 'required'],
            [['smpd_smp_id', 'smpd_aktif', 'smpd_created_by', 'smpd_updated_by', 'smpd_deleted_by'], 'integer'],
            [['smpd_created_at', 'smpd_updated_at', 'smpd_deleted_at'], 'safe'],
            [['smpd_alias'], 'string', 'max' => 50],
            [['smpd_nama'], 'string', 'max' => 100],
        ];
    }
    public function attributeLabels()
    {
        return [
            'smpd_id' => 'Smpd ID',
            'smpd_smp_id' => 'Smpd Smp ID',
            'smpd_alias' => 'Smpd Alias',
            'smpd_nama' => 'Smpd Nama',
            'smpd_aktif' => 'Smpd Aktif',
            'smpd_created_at' => 'Smpd Created At',
            'smpd_created_by' => 'Smpd Created By',
            'smpd_updated_at' => 'Smpd Updated At',
            'smpd_updated_by' => 'Smpd Updated By',
            'smpd_deleted_at' => 'Smpd Deleted At',
            'smpd_deleted_by' => 'Smpd Deleted By',
        ];
    }
    public static function find()
    {
        return new BaseQuery(get_called_class());
    }
    function getPangkat()
    {
        return $this->hasOne(Pangkat::className(),['smp_id'=>'smpd_smp_id']);
    }
    static function all()
    {
        return self::find()->select('smpd_id,smpd_nama,smp_nama')->joinWith(['pangkat'],false)->orderBy(['smp_nama'=>SORT_ASC])->active(self::$prefix)->notDeleted(self::$prefix)->asArray()->all();
    }
}
