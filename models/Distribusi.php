<?php

namespace app\models;

use app\widgets\AuthUser;
use Yii;

/**
 * This is the model class for table "filing_distribusi".
 *
 * @property int $fd_distribusi_kode
 * @property string|null $fd_reg_kode PK Pendaftaran Registrasi
 * @property string|null $fd_reg_peminjaman PK Filing Peminjaman
 * @property string $fd_pasien_kode
 * @property int $fd_petugas_pengantar_id PK sdm_m_pegawai
 * @property int $fd_status 0 = belum kembali, 1 = Sudah Dipinjamkan, 2 = Sudah Dikembalikan
 * @property string|null $fd_keterangan
 * @property string|null $fd_created_at
 * @property int|null $fd_created_by
 * @property string|null $fd_updated_at
 * @property int|null $fd_updated_by
 * @property string|null $fd_deleted_at
 * @property int|null $fd_deleted_by
 */
class Distribusi extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public $data, $error_msg;
    static $prefix = 'fd';

    public static function tableName()
    {
        return 'filing_distribusi';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fd_pasien_kode', 'fd_petugas_pengantar_id'], 'required'],
            [['fd_distribusi_kode', 'fd_petugas_pengantar_id', 'fd_status', 'fd_created_by', 'fd_updated_by', 'fd_deleted_by'], 'integer'],
            [['fd_keterangan'], 'string'],
            [['fd_created_at', 'fd_updated_at', 'fd_deleted_at'], 'safe'],
            [['fd_reg_kode', 'fd_reg_peminjaman', 'fd_pasien_kode'], 'string', 'max' => 20],
            [['fd_distribusi_kode'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'fd_distribusi_kode' => 'Kode Distribusi Dokumen',
            'fd_reg_kode' => 'Kode Registrasi Pasien',
            'fd_reg_peminjaman' => 'Kode Registrasi Peminjaman',
            'fd_pasien_kode' => 'Kode Rekam Medis Pasien',
            'fd_petugas_pengantar_id' => 'Petugas Pengantar Rekam Medis',
            'fd_status' => 'Status',
            'fd_keterangan' => 'Keterangan Distribusi',
            'fd_created_at' => 'Fd Created At',
            'fd_created_by' => 'Fd Created By',
            'fd_updated_at' => 'Fd Updated At',
            'fd_updated_by' => 'Fd Updated By',
            'fd_deleted_at' => 'Fd Deleted At',
            'fd_deleted_by' => 'Fd Deleted By',
        ];
    }
    static function find()
    {
        return new BaseQuery(get_called_class());
    }

    function beforeSave($model)
    {
        if ($this->isNewRecord) {
            $this->fd_created_by = AuthUser::user()->id;
            $this->fd_created_at = date('Y-m-d H:i:s');
            $this->fd_status = 1;
            $this->fd_distribusi_kode = $this->generateKodeDistribusi();
        } else {
            $this->fd_updated_by = AuthUser::user()->id;
            $this->fd_updated_at = date('Y-m-d H:i:s');
        }
        return parent::beforeSave($model);
    }

    
    function generateKodeDistribusi()
    {
        $kodeDistribusi = NULL;
        $check = false;
        while (!$check) {           
                $characters = '0123456789BCDEFGHIJKLMNOPQRSTUVWXYZ';
                $randomString = '';
                for ($i = 0; $i < 8; $i++) {
                    $index = rand(0, strlen($characters) - 1);
                    $randomString .= $characters[$index];
                }
            $kodeDistribusi = 'D-'.$randomString;

            $count_check = self::find()->where(["fd_distribusi_kode" => $kodeDistribusi])->asArray()->limit(1)->one();
            if ($count_check == NULL) {
                $check = true;
            }
        }
        return $kodeDistribusi;
    }

    public function getPegawai()
    {
        return $this->hasOne(Pegawai::className(), ['pgw_id' => 'fd_petugas_pengantar_id']);
    }
    public function getPasien()
    {
        return $this->hasOne(Pasien::className(), ['ps_kode' => 'fd_pasien_kode']);
    }
    function getRegistrasi()
    {
        return $this->hasOne(Registrasi::className(), ['reg_kode' => 'fd_reg_kode']);
    }
    public function getPeminjamanDetail()
    {
        return $this->hasOne(PeminjamanDetail::className(), ['fpd_peminjaman_detail_kode' => 'fd_reg_peminjaman']);
    }

    function getUnit()
    {
        return $this->hasOne(Unit::className(), ['unt_id' => 'fd_unit_terakhir']);
    }


    public function getDistribusiDetail()
    {
        return $this->hasMany(DistribusiDetail::class, ['fdd_distribusi_kode' => 'fd_distribusi_kode']);
    }
}
