<?php
namespace app\models;
use Yii;
class LayananKontrol extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'pendaftaran.layanan_kontrol';
    }
    public function rules()
    {
        return [
            [['debitur_kode', 'unit_kode', 'dokter_kode', 'is_cetak', 'no_urut_surat', 'unit_asal_kode', 'is_kemo'], 'default', 'value' => null],
            [['debitur_kode', 'unit_kode', 'dokter_kode', 'is_cetak', 'no_urut_surat', 'unit_asal_kode', 'is_kemo'], 'integer'],
            [['diagnosa', 'terapi', 'alasan'], 'string'],
            [['tgl_expire', 'tgl_ke_poli'], 'safe'],
            [['pasien_kode', 'registrasi_kode', 'debitur_detail_kode', 'registrasi_lanjut_kode'], 'string', 'max' => 10],
            [['tgl_text', 'no_surat'], 'string', 'max' => 255],
            [['no_sep', 'no_rujukan'], 'string', 'max' => 100],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pasien_kode' => 'Pasien Kode',
            'registrasi_kode' => 'Registrasi Kode',
            'debitur_kode' => 'Debitur Kode',
            'debitur_detail_kode' => 'Debitur Detail Kode',
            'unit_kode' => 'Unit Kode',
            'diagnosa' => 'Diagnosa',
            'terapi' => 'Terapi',
            'alasan' => 'Alasan',
            'dokter_kode' => 'Dokter Kode',
            'is_cetak' => 'Is Cetak',
            'tgl_expire' => 'Tgl Expire',
            'registrasi_lanjut_kode' => 'Registrasi Lanjut Kode',
            'tgl_text' => 'Tgl Text',
            'no_surat' => 'No Surat',
            'no_urut_surat' => 'No Urut Surat',
            'unit_asal_kode' => 'Unit Asal Kode',
            'no_sep' => 'No Sep',
            'no_rujukan' => 'No Rujukan',
            'tgl_ke_poli' => 'Tgl Ke Poli',
            'is_kemo' => 'Is Kemo',
        ];
    }
    public static function find()
    {
        return new BaseQuery(get_called_class());
    }
    static function getListKontrol($rm)
    {
        $query="
            SELECT * FROM (
                SELECT 
                    ROW_NUMBER() OVER ( PARTITION BY pasien_kode, unit_kode ORDER BY tgl_expire DESC ) AS rn,id,tgl_expire,no_surat
                FROM ".self::tableName()."
                WHERE pasien_kode = :nopasien AND no_surat IS NOT NULL AND registrasi_lanjut_kode IS NULL
                AND TO_CHAR( (tgl_expire + INTERVAL '".Yii::$app->params['kontrol']['countdown_day']." day') :: DATE,'YYYY-MM-DD') >= '".date('Y-m-d')."'
            ) AS t WHERE t.rn=1 ORDER BY t.tgl_expire DESC
        ";
        return Yii::$app->db->createCommand($query)->bindValues([':nopasien'=>$rm])->queryAll();
    }
}