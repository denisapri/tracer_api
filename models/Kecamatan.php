<?php
namespace app\models;
use Yii;
class Kecamatan extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'sdm_m_kecamatan';
    }
    public function rules()
    {
        return [
            [['kec_kode', 'kec_kab_kode', 'kec_aktif'], 'required'],
            [['kec_kode', 'kec_kab_kode', 'kec_aktif', 'kec_created_by', 'kec_updated_by', 'kec_deleted_by'], 'integer'],
            [['kec_created_at', 'kec_updated_at', 'kec_deleted_at'], 'safe'],
            [['kec_nama'], 'string', 'max' => 100],
            [['kec_kode'], 'unique'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'kec_kode' => 'Kec Kode',
            'kec_nama' => 'Kec Nama',
            'kec_kab_kode' => 'Kec Kab Kode',
            'kec_aktif' => 'Kec Aktif',
            'kec_created_at' => 'Kec Created At',
            'kec_created_by' => 'Kec Created By',
            'kec_updated_at' => 'Kec Updated At',
            'kec_updated_by' => 'Kec Updated By',
            'kec_deleted_at' => 'Kec Deleted At',
            'kec_deleted_by' => 'Kec Deleted By',
        ];
    }
    public static function find()
    {
        return new BaseQuery(get_called_class());
    }
    function getKabupaten()
    {
        return $this->hasOne(Kabupaten::className(),['kab_kode'=>'kec_kab_kode']);
    }
    function getKelurahan()
    {
        return $this->hasOne(Kelurahan::className(),['kel_kec_kode'=>'kec_kode']);
    }
}