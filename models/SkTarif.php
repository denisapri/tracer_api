<?php
namespace app\models;
use Yii;
class SkTarif extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'medis_sk_tarif';
    }
    public function rules()
    {
        return [
            [['skt_nomor', 'skt_tanggal', 'skt_aktif'], 'required'],
            [['skt_tanggal', 'skt_created_at', 'skt_updated_at', 'skt_deleted_at'], 'safe'],
            [['skt_aktif', 'skt_created_by', 'skt_updated_by', 'skt_deleted_by'], 'integer'],
            [['skt_ket'], 'string'],
            [['skt_nomor'], 'string', 'max' => 100],
        ];
    }
    public function attributeLabels()
    {
        return [
            'skt_id' => 'Skt ID',
            'skt_nomor' => 'Skt Nomor',
            'skt_tanggal' => 'Skt Tanggal',
            'skt_aktif' => 'Skt Aktif',
            'skt_ket' => 'Skt Ket',
            'skt_created_at' => 'Skt Created At',
            'skt_created_by' => 'Skt Created By',
            'skt_updated_at' => 'Skt Updated At',
            'skt_updated_by' => 'Skt Updated By',
            'skt_deleted_at' => 'Skt Deleted At',
            'skt_deleted_by' => 'Skt Deleted By',
        ];
    }
}
