<?php
namespace app\models;
use Yii;
class RiwayatPenempatan extends \yii\db\ActiveRecord
{
    static $prefix="rwp";
    public static function tableName()
    {
        return 'sdm_riwayat_penempatan';
    }
    public function rules()
    {
        return [
            [['rwp_pgw_id', 'rwp_nomor_surat', 'rwp_tanggal_surat', 'rwp_jabatan_penempatan'], 'required'],
            [['rwp_pgw_id', 'rwp_unit_penempatan', 'rwp_jabatan_penempatan', 'rwp_status_aktif', 'rwp_created_by', 'rwp_updated_by', 'rwp_deleted_by'], 'integer'],
            [['rwp_tanggal_surat', 'rwp_created_at', 'rwp_updated_at', 'rwp_deleted_at'], 'safe'],
            [['rwp_nomor_surat'], 'string', 'max' => 60],
        ];
    }
    public function attributeLabels()
    {
        return [
            'rwp_id' => 'Rwp ID',
            'rwp_pgw_id' => 'Rwp Pgw ID',
            'rwp_nomor_surat' => 'Rwp Nomor Surat',
            'rwp_tanggal_surat' => 'Rwp Tanggal Surat',
            'rwp_unit_penempatan' => 'Rwp Unit Penempatan',
            'rwp_jabatan_penempatan' => 'Rwp Jabatan Penempatan',
            'rwp_status_aktif' => 'Rwp Status Aktif',
            'rwp_created_at' => 'Rwp Created At',
            'rwp_created_by' => 'Rwp Created By',
            'rwp_updated_at' => 'Rwp Updated At',
            'rwp_updated_by' => 'Rwp Updated By',
            'rwp_deleted_at' => 'Rwp Deleted At',
            'rwp_deleted_by' => 'Rwp Deleted By',
        ];
    }
    public static function find()
    {
        return new BaseQuery(get_called_class());
    }
}
