<?php
namespace app\models;
use Yii;
use app\widgets\AuthUser;
class Rujukan extends \yii\db\ActiveRecord
{
    static $prefix="ruj";
    public $error_msg,$is_new;
    public static function tableName()
    {
        return 'pendaftaran_rujukan';
    }
    public function rules()
    {
        return [
            [['ruj_pasien_kode','ruj_no_kartu','ruj_nama','ruj_no_sep','ruj_tgl_rujukan','ruj_ppk_dirujuk_kode','ruj_diagnosa_kode','ruj_poli_kode','ruj_catatan','ruj_jkel'], 'required','message'=>'{attribute} harus diisi'],

            [['ruj_tgl_rujukan', 'ruj_created_at', 'ruj_updated_at', 'ruj_deleted_at'], 'safe'],
            [['ruj_ppk_dirujuk_tingkat', 'ruj_tipe_rujukan', 'ruj_is_bridging', 'ruj_created_by', 'ruj_updated_by', 'ruj_deleted_by'], 'integer'],
            [['ruj_catatan'], 'string'],
            [['ruj_pasien_kode'], 'string', 'max' => 8],
            [['ruj_no_rujukan', 'ruj_no_sep', 'ruj_no_kartu'], 'string', 'max' => 50],
            [['ruj_nama', 'ruj_ppk_dirujuk_nama', 'ruj_poli_nama','ruj_diagnosa_nama'], 'string', 'max' => 255],
            [['ruj_jkel', 'ruj_jenis_pelayanan'], 'string', 'max' => 1],
            [['ruj_ppk_dirujuk_kode', 'ruj_poli_kode'], 'string', 'max' => 100],
            [['ruj_diagnosa_kode'], 'string', 'max' => 100],
        ];
    }
    public function attributeLabels()
    {
        return [
            'ruj_id' => 'ID',
            'ruj_pasien_kode' => 'No. Rekam Medis',
            'ruj_no_rujukan' => 'No. Rujukan',
            'ruj_no_sep' => 'No. SEP',
            'ruj_nama' => 'Nama Pasien',
            'ruj_no_kartu' => 'No. Kartu',
            'ruj_jkel' => 'Jenis Kelamin',
            'ruj_tgl_rujukan' => 'Tgl. Rujukan',
            'ruj_ppk_dirujuk_tingkat' => 'Tingkat PPK Dirujuk',
            'ruj_ppk_dirujuk_kode' => 'Nama PPK Dirujuk',
            'ruj_ppk_dirujuk_nama' => 'Ruj Ppk Dirujuk Nama',
            'ruj_jenis_pelayanan' => 'Jenis Pelayanan',
            'ruj_diagnosa_kode' => 'Diagnosa',
            'ruj_diagnosa_nama' => 'Ruj Diagnosa Nama',
            'ruj_poli_kode' => 'Poli Rujukan',
            'ruj_poli_nama' => 'Ruj Poli Nama',
            'ruj_tipe_rujukan' => 'Tipe Rujukan',
            'ruj_is_bridging' => 'Gunakan Bridging ?',
            'ruj_catatan' => 'Catatan',
            'ruj_created_at' => 'Ruj Created At',
            'ruj_created_by' => 'Ruj Created By',
            'ruj_updated_at' => 'Ruj Updated At',
            'ruj_updated_by' => 'Ruj Updated By',
            'ruj_deleted_at' => 'Ruj Deleted At',
            'ruj_deleted_by' => 'Ruj Deleted By',
        ];
    }
    public static function find()
    {
        return new BaseQuery(get_called_class());
    }
    function beforeValidate()
    {
        if($this->ruj_ppk_dirujuk_kode!=NULL){
            $e=explode('|#|',$this->ruj_ppk_dirujuk_kode);
            $this->ruj_ppk_dirujuk_kode=$e[0];
            $this->ruj_ppk_dirujuk_nama=$e[1];
        }
        if($this->ruj_poli_kode!=NULL){
            $e=explode('|#|',$this->ruj_poli_kode);
            $this->ruj_poli_kode=$e[0];
            $this->ruj_poli_nama=$e[1];
        }
        if($this->ruj_diagnosa_kode!=NULL){
            $e=explode('|#|',$this->ruj_diagnosa_kode);
            $this->ruj_diagnosa_kode=$e[0];
            $this->ruj_diagnosa_nama=$e[1];
        }
        return parent::beforeValidate();
    }
    function beforeSave($model)
    {
        if($this->isNewRecord){
            $this->ruj_created_at=date('Y-m-d H:i:s');
        }
        $this->ruj_tgl_rujukan=date('Y-m-d',strtotime($this->ruj_tgl_rujukan));
        return parent::beforeSave($model);
    }
    function attr()
    {
        $data=[];
        foreach($this->attributeLabels() as $key => $val){
            $data[$val]=$this->{$key};
        }
        return $data;
    }
    function getPasien()
    {
        return $this->hasOne(Pasien::className(),['ps_kode'=>'ruj_pasien_kode']);
    }
    function saveRujukan()
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
            //insert log
            Log::saveLog($this->is_new ? "Buat Rujukan BPJS" : "Update Rujukan BPJS",$log);
            //bridging true
            if($this->ruj_is_bridging){
                //brigding insert/update rujukan
                if(!$this->bridgingRujukan()){
                    $transaction->rollBack();
                    return false;
                }
                //update no rujukan
                Yii::$app->db->createCommand()->update(self::tableName(), [
                    'ruj_no_rujukan'=>$this->ruj_no_rujukan,
                ],['ruj_id'=>$this->ruj_id])->execute();
            }
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
    function bridgingRujukan()
    {
        $br = new Bridging();
        if($this->ruj_no_rujukan==NULL){
            if($br->insertRujukan($this)->exec()){
                $data=$br->getResponse();
                $this->ruj_no_rujukan=$data->rujukan->noRujukan;
                return true;
            }
        }else{
            if($br->updateRujukan($this)->exec()){
                return true;
            }
        }
        $this->error_msg="BPJS : ".$br->errorMessage;
        return false;
    }
    function deleteRujukan()
    {
        $transaction = self::getDb()->beginTransaction();
        try {
            $log=$this->attr();
            $this->delete();
            if(!$this->bridgingDeleteRujukan()){
                $transaction->rollBack();
                return false;
            }
            Log::saveLog("Hapus Rujukan BPJS",$log);
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
    function bridgingDeleteRujukan()
    {
        $br = new Bridging();
        if($br->deleteRujukan([
            'request'=>[
                't_rujukan'=>[
                    'noRujukan'=>$this->ruj_no_rujukan,
                    'user'=>(string) AuthUser::user()->id,
                ]
            ]
        ])->exec()){
            return true;
        }
        $this->error_msg=$br->errorMessage;
        return false;
    }
}
