<?php
namespace app\models;
use Yii;
class KirimanDetail extends \yii\db\ActiveRecord
{
    static $prefix="pmkd";
    public static function tableName()
    {
        return 'pendaftaran_m_kiriman_detail';
    }
    public function rules()
    {
        return [
            [['pmkd_kode', 'pmkd_pmkr_kode', 'pmkd_nama', 'pmkd_aktif'], 'required'],
            [['pmkd_aktif', 'pmkd_created_by', 'pmkd_updated_by', 'pmkd_deleted_by'], 'integer'],
            [['pmkd_created_at', 'pmkd_updated_at', 'pmkd_deleted_at'], 'safe'],
            [['pmkd_kode', 'pmkd_pmkr_kode'], 'string', 'max' => 10],
            [['pmkd_nama'], 'string', 'max' => 255],
            [['pmkd_kode'], 'unique'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'pmkd_kode' => 'Pmkd Kode',
            'pmkd_pmkr_kode' => 'Pmkd Pmkr Kode',
            'pmkd_nama' => 'Pmkd Nama',
            'pmkd_aktif' => 'Pmkd Aktif',
            'pmkd_created_at' => 'Pmkd Created At',
            'pmkd_created_by' => 'Pmkd Created By',
            'pmkd_updated_at' => 'Pmkd Updated At',
            'pmkd_updated_by' => 'Pmkd Updated By',
            'pmkd_deleted_at' => 'Pmkd Deleted At',
            'pmkd_deleted_by' => 'Pmkd Deleted By',
        ];
    }
    public static function find()
    {
        return new BaseQuery(get_called_class());
    }
}