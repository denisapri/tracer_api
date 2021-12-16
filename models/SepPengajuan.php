<?php
namespace app\models;
use Yii;
use app\widgets\AuthUser;
class SepPengajuan extends \yii\db\ActiveRecord
{
    public $error_msg,$is_new;
    static $prefix="psp";
    public static function tableName()
    {
        return 'pendaftaran_sep_pengajuan';
    }
    public function rules()
    {
        return [
            [['psp_pasien_kode', 'psp_no_kartu', 'psp_tgl_sep', 'psp_jenis_pelayanan'], 'required','message'=>'{attribute} harus diisi'],
            [['psp_tgl_sep','psp_approved_at', 'psp_created_at', 'psp_updated_at', 'psp_deleted_at'], 'safe'],
            [['psp_jenis_pelayanan', 'psp_status', 'psp_created_by', 'psp_updated_by', 'psp_deleted_by','psp_approved_by'], 'integer'],
            [['psp_ket_pengajuan', 'psp_ket_approval'], 'string'],
            [['psp_pasien_kode'], 'string', 'max' => 10],
            [['psp_no_kartu'], 'string', 'max' => 50],
        ];
    }
    public function attributeLabels()
    {
        return [
            'psp_id' => 'Psp ID',
            'psp_pasien_kode' => 'No. Rekam Medis',
            'psp_no_kartu' => 'No. Kartu',
            'psp_tgl_sep' => 'Tgl. SEP',
            'psp_jenis_pelayanan' => 'Jenis Pelayanan',
            'psp_ket_pengajuan' => 'Catatan Pengajuan SEP (Petugas RM)',
            'psp_status' => 'Status',
            'psp_ket_approval' => 'Catatan Approval',
            'psp_approved_at'=>'psp_approved_at',
            'psp_approved_by'=>'psp_approved_by',
            'psp_created_at' => 'Psp Created At',
            'psp_created_by' => 'Psp Created By',
            'psp_updated_at' => 'Psp Updated At',
            'psp_updated_by' => 'Psp Updated By',
            'psp_deleted_at' => 'Psp Deleted At',
            'psp_deleted_by' => 'Psp Deleted By',
        ];
    }
    public static function find()
    {
        return new BaseQuery(get_called_class());
    }
    function attr()
    {
        $data=[];
        foreach($this->attributeLabels() as $key => $val){
            $data[$val]=$this->{$key};
        }
        return $data;
    }
    function beforeSave($model)
    {
        if($this->isNewRecord){
            $this->psp_created_at=date('Y-m-d H:i:s');
        }else{
            $this->psp_approved_at=date('Y-m-d H:i:s',strtotime($this->psp_approved_at));
        }
        $this->psp_tgl_sep=date('Y-m-d',strtotime($this->psp_tgl_sep));
        return parent::beforeSave($model);
    }
    function getPasien()
    {
        return $this->hasOne(Pasien::className(),['ps_kode'=>'psp_pasien_kode']);
    }
    function savePengajuan()
    {
        $transaction = self::getDb()->beginTransaction();
        try {
            //set data log
            $log=[];
            if(!$this->is_new){
                $log['sebelum']=$this->attr();
            }
            $this->save(false);
            if(!$this->is_new){
                $log['sesudah']=$this->attr();
            }else{
                $log=$this->attr();
            }
            //brigding insert/update rujukan
            if($this->is_new){
                if(!$this->bridgingPengajuan()){
                    $transaction->rollBack();
                    return false;
                }
            }else{
                if($this->psp_status=='2'){
                    if(!$this->bridgingPengajuan(true)){
                        $transaction->rollBack();
                        return false;
                    }
                }
            }
            //insert log
            Log::saveLog(($this->is_new ? "Buat" : "Update")." Pengajuan SEP BPJS",$log);
            $transaction->commit();
            return true;
        } catch(\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch(\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
    function bridgingPengajuan($approve=false)
    {
        $br = new Bridging();
        $data=[
            'request'=>[
                't_sep'=>[
                    'noKartu'=>(string) $this->psp_no_kartu,
                    'tglSep'=>(string) $this->psp_tgl_sep,
                    'jnsPelayanan'=>(string) $this->psp_jenis_pelayanan,
                    'keterangan'=>(string) $this->psp_ket_pengajuan,
                    'user'=>(string) AuthUser::user()->id,
                ]
            ]
        ];
        if(!$approve){
            if($br->pengajuanSEP($data)->exec()){
                return true;
            }
        }else{
            if($br->approvalSEP($data)->exec()){
                return true;
            }
        }
        $this->error_msg="BPJS : ".$br->errorMessage;
        return false;
    }
}
