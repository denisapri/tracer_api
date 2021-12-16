<?php
namespace app\models;
use Yii;
class Aplikasi extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'sdm_m_aplikasi';
    }
    public function rules()
    {
        return [
            [['apl_kode', 'apl_nama', 'apl_created_by'], 'required'],
            [['apl_aktif', 'apl_created_by', 'apl_updated_by', 'apl_deleted_by'], 'integer'],
            [['apl_created_at', 'apl_updated_at', 'apl_deleted_at'], 'safe'],
            [['apl_kode'], 'string', 'max' => 20],
            [['apl_nama'], 'string', 'max' => 100],
            [['apl_kode'], 'unique'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'apl_id' => 'Apl ID',
            'apl_kode' => 'Apl Kode',
            'apl_nama' => 'Apl Nama',
            'apl_aktif' => 'Apl Aktif',
            'apl_created_at' => 'Apl Created At',
            'apl_created_by' => 'Apl Created By',
            'apl_updated_at' => 'Apl Updated At',
            'apl_updated_by' => 'Apl Updated By',
            'apl_deleted_at' => 'Apl Deleted At',
            'apl_deleted_by' => 'Apl Deleted By',
        ];
    }
}