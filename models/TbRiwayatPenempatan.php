<?php
namespace app\models;
use Yii;
class TbRiwayatPenempatan extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'pegawai.tb_riwayat_penempatan';
    }
    public function rules()
    {
        return [
            [['id_nip_nrp', 'nota_dinas', 'tanggal', 'penempatan'], 'required'],
            [['tanggal'], 'safe'],
            [['atasan_langsung', 'penempatan', 'sdm_rumpun', 'sdm_sub_rumpun', 'sdm_jenis', 'unit_kerja'], 'default', 'value' => null],
            [['atasan_langsung', 'penempatan', 'sdm_rumpun', 'sdm_sub_rumpun', 'sdm_jenis', 'unit_kerja'], 'integer'],
            [['dokumen'], 'string'],
            [['id_nip_nrp', 'niptk'], 'string', 'max' => 30],
            [['nota_dinas'], 'string', 'max' => 60],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_nip_nrp' => 'Id Nip Nrp',
            'nota_dinas' => 'Nota Dinas',
            'tanggal' => 'Tanggal',
            'atasan_langsung' => 'Atasan Langsung',
            'penempatan' => 'Penempatan',
            'sdm_rumpun' => 'Sdm Rumpun',
            'sdm_sub_rumpun' => 'Sdm Sub Rumpun',
            'sdm_jenis' => 'Sdm Jenis',
            'dokumen' => 'Dokumen',
            'unit_kerja' => 'Unit Kerja',
            'niptk' => 'Niptk',
        ];
    }
    function getJenis()
    {
        return $this->hasOne(DmSdmJenis::className(),['kode'=>'sdm_jenis']);
    }
}