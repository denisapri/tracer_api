<?php
namespace app\models;
use Yii;
class Provinsi extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'sdm_m_provinsi';
    }
    public function rules()
    {
        return [
            [['prv_kode', 'prv_aktif'], 'required'],
            [['prv_kode', 'prv_aktif', 'prv_created_by', 'prv_updated_by', 'prv_deleted_by'], 'integer'],
            [['prv_created_at', 'prv_updated_at', 'prv_deleted_at'], 'safe'],
            [['prv_nama'], 'string', 'max' => 100],
            [['prv_kode'], 'unique'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'prv_kode' => 'Prv Kode',
            'prv_nama' => 'Prv Nama',
            'prv_aktif' => 'Prv Aktif',
            'prv_created_at' => 'Prv Created At',
            'prv_created_by' => 'Prv Created By',
            'prv_updated_at' => 'Prv Updated At',
            'prv_updated_by' => 'Prv Updated By',
            'prv_deleted_at' => 'Prv Deleted At',
            'prv_deleted_by' => 'Prv Deleted By',
        ];
    }
    public static function find()
    {
        return new BaseQuery(get_called_class());
    }
    public function getKabupaten()
    {
        return $this->hasMany(Kabupaten::className(), ['kab_prv_kode' => 'prv_kode']);
    }
}