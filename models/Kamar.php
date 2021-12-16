<?php
namespace app\models;
use Yii;
class Kamar extends \yii\db\ActiveRecord
{
    static $prefix="kmr";
    public static function tableName()
    {
        return 'medis_kamar';
    }
    public function rules()
    {
        return [
            [['kmr_unt_id', 'kmr_kr_kode', 'kmr_no_kamar', 'kmr_no_kasur', 'kmr_aktif'], 'required'],
            [['kmr_unt_id', 'kmr_aktif', 'kmr_created_by', 'kmr_updated_by', 'kmr_deleted_by'], 'integer'],
            [['kmr_created_at', 'kmr_updated_at', 'kmr_deleted_at'], 'safe'],
            [['kmr_kr_kode'], 'string', 'max' => 3],
            [['kmr_no_kamar', 'kmr_no_kasur'], 'string', 'max' => 100],
        ];
    }
    public function attributeLabels()
    {
        return [
            'kmr_id' => 'Kmr ID',
            'kmr_unt_id' => 'Kmr Unt ID',
            'kmr_kr_kode' => 'Kmr Kr Kode',
            'kmr_no_kamar' => 'Kmr No Kamar',
            'kmr_no_kasur' => 'Kmr No Kasur',
            'kmr_aktif' => 'Kmr Aktif',
            'kmr_created_at' => 'Kmr Created At',
            'kmr_created_by' => 'Kmr Created By',
            'kmr_updated_at' => 'Kmr Updated At',
            'kmr_updated_by' => 'Kmr Updated By',
            'kmr_deleted_at' => 'Kmr Deleted At',
            'kmr_deleted_by' => 'Kmr Deleted By',
        ];
    }
    public static function find()
    {
        return new BaseQuery(get_called_class());
    }
    function getKelas()
    {
        return $this->hasOne(KelasRawat::className(),['kr_kode'=>'kmr_kr_kode']);
    }
    function getLayanan()
    {
        return $this->hasMany(Layanan::className(),['pl_kamar_id'=>'kmr_id']);
    }
    function getUnit()
    {
        return $this->hasOne(Unit::className(),['unt_id'=>'kmr_unt_id']);
    }
    function getTarif()
    {
        return $this->hasOne(TarifKamar::className(),['tkr_kmr_id'=>'kmr_id']);
    }
}