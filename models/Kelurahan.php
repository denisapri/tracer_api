<?php
namespace app\models;
use Yii;
class Kelurahan extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'sdm_m_kelurahan_desa';
    }
    public function rules()
    {
        return [
            [['kel_kode', 'kel_kec_kode', 'kel_aktif'], 'required'],
            [['kel_kode', 'kel_kec_kode', 'kel_aktif', 'kel_created_by', 'kel_updated_by', 'kel_deleted_by'], 'integer'],
            [['kel_created_at', 'kel_updated_at', 'kel_deleted_at'], 'safe'],
            [['kel_nama'], 'string', 'max' => 100],
            [['kel_kode'], 'unique'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'kel_kode' => 'Kel Kode',
            'kel_nama' => 'Kel Nama',
            'kel_kec_kode' => 'Kel Kec Kode',
            'kel_aktif' => 'Kel Aktif',
            'kel_created_at' => 'Kel Created At',
            'kel_created_by' => 'Kel Created By',
            'kel_updated_at' => 'Kel Updated At',
            'kel_updated_by' => 'Kel Updated By',
            'kel_deleted_at' => 'Kel Deleted At',
            'kel_deleted_by' => 'Kel Deleted By',
        ];
    }
    public static function find()
    {
        return new BaseQuery(get_called_class());
    }
    function getKecamatan()
    {
        return $this->hasOne(Kecamatan::className(),['kec_kode'=>'kel_kec_kode']);
    }
}
