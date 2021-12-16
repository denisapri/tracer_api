<?php

namespace app\models;

use app\widgets\AuthUser;
use Yii;

class Registrasi extends \yii\db\ActiveRecord
{
    public $kunjungan, $unit, $data, $error_msg, $kiriman, $debitur, $nama;
    public static $igd = 1, $rj = 2, $ri = 3;
    static $prefix = 'reg';
    public static function tableName()
    {
        return 'pendaftaran_registrasi';
    }
    public function rules()
    {
        return [
            [['kunjungan', 'reg_pasien_kode', 'kiriman', 'reg_pmkd_kode', 'debitur', 'reg_pmdd_kode', 'unit'], 'required', 'on' => 'daftar_baru', 'message' => '{attribute} harus diisi'],
            [['kunjungan', 'reg_pasien_kode', 'kiriman', 'reg_pmkd_kode', 'debitur', 'reg_pmdd_kode', 'unit'], 'required', 'on' => 'daftar_update', 'message' => '{attribute} harus diisi'],

            [['debitur', 'reg_pmdd_kode'], 'required', 'on' => 'penanggung_update', 'message' => '{attribute} harus diisi'],

            [['reg_tgl_masuk', 'reg_tgl_keluar', 'reg_created_at', 'reg_updated_at', 'reg_deleted_at'], 'safe'],
            [['kunjungan', 'reg_is_print', 'reg_created_by', 'reg_updated_by', 'reg_deleted_by'], 'integer'],
            [['reg_kode', 'reg_pasien_kode', 'reg_pmkd_kode', 'reg_pmdd_kode'], 'string', 'max' => 10],
            [['reg_no_sep'], 'string', 'max' => 50],
            [['reg_kode'], 'unique'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'reg_kode' => 'No. Registrasi',
            'reg_pasien_kode' => 'No. Rekam Medis',
            'reg_tgl_masuk' => 'Tgl Masuk',
            'reg_tgl_keluar' => 'Tgl Keluar',
            'reg_pmkd_kode' => 'Detail Kiriman Dari',
            'reg_pmdd_kode' => 'Detail Cara Bayar',
            'reg_no_sep' => 'No Sep',
            'reg_is_print' => 'Reg Is Print',
            'reg_created_at' => 'Reg Created At',
            'reg_created_by' => 'Reg Created By',
            'reg_updated_at' => 'Reg Updated At',
            'reg_updated_by' => 'Reg Updated By',
            'reg_deleted_at' => 'Reg Deleted At',
            'reg_deleted_by' => 'Reg Deleted By',
            'kunjungan' => 'Kunjungan'
        ];
    }
    static function find()
    {
        return new BaseQuery(get_called_class());
    }
    public function behaviors()
    {
        return [
            [
                'class' => TrimBehavior::className(),
            ],
        ];
    }
    function attr()
    {
        $data = [];
        foreach ($this->attributeLabels() as $key => $val) {
            $data[$val] = $this->{$key};
        }
        return $data;
    }
    function beforeValidate()
    {
        if ($this->scenario == "daftar_baru") {
            $this->reg_tgl_masuk = date('Y-m-d H:i:s');
        }
        return parent::beforeValidate();
    }

    function getLayanan()
    {
        return $this->hasMany(Layanan::className(), ['pl_reg_kode' => 'reg_kode']);
    }
    function getLayananhasone()
    {
        return $this->hasOne(Layanan::className(), ['pl_reg_kode' => 'reg_kode']);
    }
    public function getDebiturdetail()
    {
        return $this->hasOne(DebiturDetail::className(), ['pmdd_kode' => 'reg_pmdd_kode']);
    }
    public function getKirimandetail()
    {
        return $this->hasOne(KirimanDetail::className(), ['pmkd_kode' => 'reg_pmkd_kode']);
    }
    public function getPasien()
    {
        return $this->hasOne(Pasien::className(), ['ps_kode' => 'reg_pasien_kode']);
    }
    function getDistribusi()
    {
        return $this->hasMany(Distribusi::className(), ['fd_reg_kode' => 'reg_kode']);
    }
}
