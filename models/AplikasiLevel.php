<?php
namespace app\models;
use Yii;
class AplikasiLevel extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'sdm_m_aplikasi_level';
    }
    public function rules()
    {
        return [
            [['all_apl_id', 'all_nama', 'all_created_by'], 'required'],
            [['all_apl_id', 'all_aktif', 'all_created_by', 'all_updated_by', 'all_deleted_by'], 'integer'],
            [['all_created_at', 'all_updated_at', 'all_deleted_at'], 'safe'],
            [['all_nama'], 'string', 'max' => 100],
        ];
    }
    public function attributeLabels()
    {
        return [
            'all_id' => 'All ID',
            'all_apl_id' => 'All Apl ID',
            'all_nama' => 'All Nama',
            'all_aktif' => 'All Aktif',
            'all_created_at' => 'All Created At',
            'all_created_by' => 'All Created By',
            'all_updated_at' => 'All Updated At',
            'all_updated_by' => 'All Updated By',
            'all_deleted_at' => 'All Deleted At',
            'all_deleted_by' => 'All Deleted By',
        ];
    }
}