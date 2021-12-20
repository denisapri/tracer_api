<?php

namespace app\models;

use Yii;

class Pegawai extends \yii\db\ActiveRecord
{
    static $prefix = "pgw";
    public static function tableName()
    {
        return 'sdm_m_pegawai';
    }
    public function rules()
    {
        return [
            [['pgw_nomor', 'pgw_gelar_depan', 'pgw_nama', 'pgw_gelar_belakang'], 'required'],
            [['pgw_tanggal_lahir', 'pgw_created_at', 'pgw_updated_at', 'pgw_deleted_at'], 'safe'],
            [['pgw_kode_pos', 'pgw_tinggi_badan', 'pgw_berat_badan', 'pgw_status_kepegawaian_id', 'pgw_aktif', 'pgw_tipe_user', 'pgw_created_by', 'pgw_updated_by', 'pgw_deleted_by'], 'integer'],
            [['pgw_nomor', 'pgw_nama', 'pgw_tempat_lahir', 'pgw_desa_kelurahan', 'pgw_kecamatan', 'pgw_kabupaten_kota', 'pgw_provinsi'], 'string', 'max' => 30],
            [['pgw_gelar_depan', 'pgw_gelar_belakang', 'pgw_jenis_kelamin'], 'string', 'max' => 10],
            [['pgw_email'], 'string', 'max' => 255],
            [['pgw_status_perkawinan'], 'string', 'max' => 20],
            [['pgw_agama_id', 'pgw_no_telepon_1', 'pgw_no_telepon_2'], 'string', 'max' => 15],
            [['pgw_alamat', 'pgw_foto', 'pgw_password_hash'], 'string', 'max' => 100],
            [['pgw_rt', 'pgw_rw'], 'string', 'max' => 5],
            [['pgw_golongan_darah'], 'string', 'max' => 3],
            [['pgw_npwp', 'pgw_nomor_ktp', 'pgw_rambut', 'pgw_bentuk_muka', 'pgw_warna_kulit', 'pgw_ciri_ciri_khas', 'pgw_cacat_tubuh', 'pgw_kegemaran_1', 'pgw_kegemaran_2', 'pgw_username', 'pgw_auth_key', 'pgw_password_reset_token'], 'string', 'max' => 50],
        ];
    }
    public function attributeLabels()
    {
        return [
            'pgw_id' => 'Pgw ID',
            'pgw_nomor' => 'Pgw Nomor',
            'pgw_gelar_depan' => 'Pgw Gelar Depan',
            'pgw_nama' => 'Pgw Nama',
            'pgw_gelar_belakang' => 'Pgw Gelar Belakang',
            'pgw_email' => 'Pgw Email',
            'pgw_tempat_lahir' => 'Pgw Tempat Lahir',
            'pgw_tanggal_lahir' => 'Pgw Tanggal Lahir',
            'pgw_jenis_kelamin' => 'Pgw Jenis Kelamin',
            'pgw_status_perkawinan' => 'Pgw Status Perkawinan',
            'pgw_agama_id' => 'Pgw Agama ID',
            'pgw_alamat' => 'Pgw Alamat',
            'pgw_rt' => 'Pgw Rt',
            'pgw_rw' => 'Pgw Rw',
            'pgw_desa_kelurahan' => 'Pgw Desa Kelurahan',
            'pgw_kecamatan' => 'Pgw Kecamatan',
            'pgw_kabupaten_kota' => 'Pgw Kabupaten Kota',
            'pgw_provinsi' => 'Pgw Provinsi',
            'pgw_kode_pos' => 'Pgw Kode Pos',
            'pgw_no_telepon_1' => 'Pgw No Telepon 1',
            'pgw_no_telepon_2' => 'Pgw No Telepon 2',
            'pgw_golongan_darah' => 'Pgw Golongan Darah',
            'pgw_npwp' => 'Pgw Npwp',
            'pgw_nomor_ktp' => 'Pgw Nomor Ktp',
            'pgw_tinggi_badan' => 'Pgw Tinggi Badan',
            'pgw_berat_badan' => 'Pgw Berat Badan',
            'pgw_rambut' => 'Pgw Rambut',
            'pgw_bentuk_muka' => 'Pgw Bentuk Muka',
            'pgw_warna_kulit' => 'Pgw Warna Kulit',
            'pgw_ciri_ciri_khas' => 'Pgw Ciri Ciri Khas',
            'pgw_cacat_tubuh' => 'Pgw Cacat Tubuh',
            'pgw_kegemaran_1' => 'Pgw Kegemaran 1',
            'pgw_kegemaran_2' => 'Pgw Kegemaran 2',
            'pgw_foto' => 'Pgw Foto',
            'pgw_status_kepegawaian_id' => 'Pgw Status Kepegawaian ID',
            'pgw_aktif' => 'Pgw Aktif',
            'pgw_tipe_user' => 'Pgw Tipe User',
            'pgw_username' => 'Pgw Username',
            'pgw_auth_key' => 'Pgw Auth Key',
            'pgw_password_hash' => 'Pgw Password Hash',
            'pgw_password_reset_token' => 'Pgw Password Reset Token',
            'pgw_created_at' => 'Pgw Created At',
            'pgw_created_by' => 'Pgw Created By',
            'pgw_updated_at' => 'Pgw Updated At',
            'pgw_updated_by' => 'Pgw Updated By',
            'pgw_deleted_at' => 'Pgw Deleted At',
            'pgw_deleted_by' => 'Pgw Deleted By',
        ];
    }
    public static function find()
    {
        return new BaseQuery(get_called_class());
    }
    function getPenempatan()
    {
        return $this->hasMany(RiwayatPenempatan::className(), ['rwp_pgw_id' => 'pgw_id'])->orderBy(['rwp_tanggal_surat' => SORT_DESC])->limit(1);
    }
    static function listDokterRawatinap()
    {
        $query = self::find()->where(['pgw_rpn_id' => 1])->select(["pgw_id as id", "concat(COALESCE(pgw_gelar_depan,''),' ',COALESCE(pgw_nama,''),' ',COALESCE(pgw_gelar_belakang,'')) as text"])->notDeleted(self::$prefix)->asArray()->all();
        return $query;
    }
    function getRegistrasi()
    {
        return $this->hasMany(Registrasi::className(), ['reg_created_by' => 'pgw_id']);
    }
}
