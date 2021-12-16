<?php
namespace app\models;
use Yii;
use app\widgets\AuthUser;
use yii\web\NotFoundHttpException;
class Pasien extends \yii\db\ActiveRecord
{
    static 
        $prefix="ps",
        $jenis_identitas=[1=>'KTP',2=>'SIM',3=>'PASPOR',4=>'NIP'],
        $status_kawin=['t'=>'Belum Kawin','k'=>'Kawin','d'=>'Duda','j'=>'Janda'],
        $kddk=['k'=>'Kepala Keluarga','i'=>'Istri','a'=>'Anak'];
    public $umur,$kebiasaan,$riwayat_penyakit;
    public $anak_nama,$anak_tgl,$anak_status,$anak_mr;
    public $pen_nama,$pen_nomor;
    public $error_msg,$data;
    public $kunjungan,$umur_th,$umur_bln,$umur_hari;
    public static function tableName()
    {
        return 'pendaftaran_pasien';
    }
    public function rules()
    {
        return [
            [['ps_nama','ps_jenis_identitas', 'ps_no_identitas', 'ps_tempat_lahir', 'ps_tgl_lahir','ps_goldar', 'ps_agama_id', 'ps_jkel','ps_suku_id','ps_pendidikan_id', 'ps_pekerjaan_id','ps_no_telp','ps_alamat','ps_kelurahan_id','ps_kewarganegaraan_id', 'ps_marital_status'], 'required','on'=>'pasien_baru','message'=>'{attribute} harus diisi'],
            [['ps_nama','ps_jenis_identitas', 'ps_no_identitas', 'ps_tempat_lahir', 'ps_tgl_lahir','ps_goldar', 'ps_agama_id', 'ps_jkel','ps_suku_id','ps_pendidikan_id', 'ps_pekerjaan_id','ps_no_telp','ps_alamat','ps_kelurahan_id','ps_kewarganegaraan_id', 'ps_marital_status'], 'required','on'=>'pasien_update','message'=>'{attribute} harus diisi'],
            
            [['ps_kesatuan_id','ps_pangkat_detail_id','ps_jenis_identitas', 'ps_agama_id', 'ps_suku_id', 'ps_pendidikan_id', 'ps_jurusan_id', 'ps_pekerjaan_id', 'ps_penghasilan', 'ps_kewarganegaraan_id', 'ps_istri_ke', 'ps_anak_ke', 'ps_jml_anak', 'ps_created_by', 'ps_updated_by', 'ps_deleted_by'], 'integer'],
            [['ps_tgl_lahir', 'ps_created_at', 'ps_updated_at', 'ps_deleted_at'], 'safe'],
            [['ps_alamat','ps_kebiasaan','ps_riwayat_penyakit'], 'string'],
            [['ps_kode', 'ps_kelurahan_id', 'ps_ayah_no_rekam_medis', 'ps_ibu_no_rekam_medis'], 'string', 'max' => 10],
            [['ps_no_identitas', 'ps_no_telp'], 'string', 'max' => 30],
            [['ps_kesatuan_nomor'],'string','max'=>20],
            [['ps_nama', 'ps_tempat_lahir', 'ps_tempat_kerja', 'ps_alamat_tempat_kerja', 'ps_nama_pasangan', 'ps_ayah_nama', 'ps_ibu_nama'], 'string', 'max' => 255],
            [['ps_jkel', 'ps_marital_status','ps_kedudukan_keluarga'], 'string', 'max' => 1],
            [['ps_goldar'], 'string', 'max' => 2],
            [['ps_kode'], 'unique'],
            [['kebiasaan','riwayat_penyakit'], 'each', 'rule' => ['integer']],
            [['anak_nama','anak_tgl','anak_status','pen_nama','pen_nomor'], 'each', 'rule' => ['string']],
            ['ps_jml_anak','checkAnak'],
            ['pen_nama','checkPenanggung'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'ps_kode' => 'No. Rekam Medis',
            'ps_no_identitas' => 'No Identitas',
            'ps_jenis_identitas' => 'Jenis Identitas',
            'ps_nama' => 'Nama Pasien',
            'ps_tgl_lahir' => 'Tgl. Lahir',
            'ps_tempat_lahir' => 'Tempat Lahir',
            'ps_jkel' => 'Jenis kelamin',
            'ps_goldar' => 'Goldar',
            'ps_agama_id' => 'Agama',
            'ps_suku_id' => 'Suku',
            'ps_pendidikan_id' => 'Pendidikan',
            'ps_jurusan_id' => 'Jurusan',
            'ps_pekerjaan_id' => 'Pekerjaan',
            'ps_kesatuan_id'=>'Kesatuan',
            'ps_kesatuan_nomor'=>'Nomor Kesatuan',
            'ps_pangkat_detail_id'=>'Pangkat',
            'ps_tempat_kerja' => 'Nama Tempat Kerja',
            'ps_alamat_tempat_kerja' => 'Alamat Tempat Kerja',
            'ps_penghasilan' => 'Penghasilan',
            'ps_alamat' => 'Alamat',
            'ps_no_telp' => 'No Telp',
            'ps_kelurahan_id' => 'Kelurahan',
            'ps_kewarganegaraan_id' => 'Kewarganegaraan',
            'ps_marital_status' => 'Marital Status',
            'ps_kedudukan_keluarga' => 'Kedudukan Keluarga',
            'ps_nama_pasangan' => 'Nama Pasangan',
            'ps_istri_ke' => 'Istri Ke',
            'ps_anak_ke' => 'Anak Ke',
            'ps_jml_anak' => 'Jml Anak',
            'ps_ayah_nama' => 'Nama Ayah',
            'ps_ayah_no_rekam_medis' => 'No Rekam Medis Ayah',
            'ps_ibu_nama' => 'Nama Ibu',
            'ps_ibu_no_rekam_medis' => 'No Rekam Medis Ibu',
            'ps_kebiasaan'=>'Kebiasaan Sehari-hari',
            'ps_riwayat_penyakit'=>'Riwayat Penyakit',
            'ps_created_at' => 'Ps Created At',
            'ps_created_by' => 'Ps Created By',
            'ps_updated_at' => 'Ps Updated At',
            'ps_updated_by' => 'Ps Updated By',
            'ps_deleted_at' => 'Ps Deleted At',
            'ps_deleted_by' => 'Ps Deleted By',
        ];
    }
    public static function find()
    {
        return new BaseQuery(get_called_class());
    }
    public function behaviors()
    {
        return [
            [
                'class'=>TrimBehavior::className(),
            ],
        ];
    }
    function beforeSave($model)
    {
        if($this->scenario=="pasien_baru"){
            if($this->ps_ayah_no_rekam_medis!=NULL){
                $this->ps_ayah_no_rekam_medis=self::formatRm($this->ps_ayah_no_rekam_medis);
            }
            if($this->ps_ibu_no_rekam_medis!=NULL){
                $this->ps_ibu_no_rekam_medis=self::formatRm($this->ps_ibu_no_rekam_medis);
            }
        }
        if($this->ps_marital_status=='t'){
            $this->ps_jml_anak=NULL;
            $this->ps_nama_pasangan=NULL;
        }
        $this->ps_tgl_lahir=date('Y-m-d',strtotime($this->ps_tgl_lahir));
        if($this->isNewRecord){
            $this->ps_created_at=date('Y-m-d H:i:s');
            $this->ps_created_by=AuthUser::user()->id;
        }else{
            $this->ps_updated_at=date('Y-m-d H:i:s');
            $this->ps_updated_by=AuthUser::user()->id;
        }
        if($this->ps_pekerjaan_id!=144){
            $this->ps_kesatuan_id=NULL;
            $this->ps_kesatuan_nomor=NULL;
            $this->ps_pangkat_detail_id=NULL;
        }
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
    function checkAnak($attribute, $params)
    {
        if(!$this->hasErrors()){
            if($this->ps_jml_anak>0){
                if($this->anak_nama==NULL && $this->anak_tgl==NULL && $this->anak_status==NULL){
                    $this->addError($attribute, 'Silahkan lengkapi data anak !');
                }
            }
        }
    }
    function checkPenanggung($attribute, $params)
    {
        if(!$this->hasErrors()){
            if($this->pen_nama>0){
                if($this->pen_nama==NULL && $this->pen_nomor==NULL){
                    $this->addError($attribute, 'Silahkan lengkapi data penanggung !');
                }
            }
        }
    }
    function getJurusan()
    {
        return $this->hasOne(Jurusan::className(),['jur_id'=>'ps_jurusan_id']);
    }
    function getPendidikan()
    {
        return $this->hasOne(Pendidikan::className(),['pdd_id'=>'ps_pendidikan_id']);
    }
    function getPekerjaan()
    {
        return $this->hasOne(Pekerjaan::className(),['pkj_id'=>'ps_pekerjaan_id']);
    }
    function getAgama()
    {
        return $this->hasOne(Agama::className(),['agm_id'=>'ps_agama_id']);
    }
    function getSuku()
    {
        return $this->hasOne(Suku::className(),['suk_id'=>'ps_suku_id']);
    }
    function getNegara()
    {
        return $this->hasOne(Negara::className(),['ngr_id'=>'ps_kewarganegaraan_id']);
    }
    function getPenanggung()
    {
        return $this->hasMany(PasienPenanggung::className(), ['pen_pasien_kode' => 'ps_kode']);
    }
    function getAnak()
    {
        return $this->hasMany(PasienAnak::className(), ['ppa_pasien_kode' => 'ps_kode']);
    }
    function getKelurahan()
    {
        return $this->hasOne(Kelurahan::className(),['kel_kode'=>'ps_kelurahan_id']);
    }
    static function formatRm($norm)
    {
        return sprintf('%0'.Yii::$app->params['rm']['length'].'d',$norm);
    }
    function getAge()
    {
        $interval = date_diff(date_create(), date_create($this->tgl_lahir));
        $this->umur_th = $interval->format('%Y');
        $this->umur_bln = $interval->format('%M');
        $this->umur_hari = $interval->format('%d');
    }
    static function getData($rm)
    {
        return self::find()->select('ps_kode as no_pasien,ps_no_identitas,ps_nama,ps_jkel,ps_alamat,ps_tgl_lahir,ps_tempat_lahir,ps_no_telp')->where(['or',['ps_kode'=>$rm],['ps_no_identitas'=>$rm]])->notDeleted(self::$prefix)->asArray()->limit(1)->one();
    }
    static function getNik($rm)
    {
        $data=self::find()->select('ps_no_identitas')->where(['ps_kode'=>$rm,'ps_jenis_identitas'=>1])->asArray()->limit(1)->one();
        if($data!=NULL){
            return $data['ps_no_identitas'];
        }
        return NULL;
    }
    function generateRm()
    {
        $debitur=NULL;
        
        //check debitur pasien
        if($this->pen_nama!=NULL){
            foreach($this->pen_nama as $n){
                $r=explode('_',$n);
                if(Yii::$app->params['bpjs']['app']['id']==$r[1]){
                    $debitur=$r[1];
                    break;
                }else{
                    $debitur=$r[1];
                }
            }
        }
        
        //get kode rm
        $data_kode_rm=KodeRm::maxKode($debitur); //get kode rm based on debitur
        
        //cek jika tidak ada kode rm
        if(count($data_kode_rm)<1){
            $this->error_msg='Tidak tersedia KODE RM, Hub. Administrator IT anda !';
            return false;
        }

        //cek maks no.rm per kode rm
        $kode_rm=NULL;
        foreach($data_kode_rm as $k){
            $max=substr(self::find()->where(['like','ps_kode',$k.'%',false])->max('ps_kode'),2);
            if($max!=9999){
                $kode_rm=$k;
                break;
            }
        }
        
        //return error msg if kode rm is full
        if($kode_rm==NULL){
            $nama=NULL;
            if($debitur!=NULL){
                $nama=DebiturDetail::find()->select('pmdd_nama')->where(['pmdd_kode'=>$debitur])->active(DebiturDetail::$prefix)->notDeleted(DebiturDetail::$prefix)->asArray()->limit(1)->one();
                $nama=$nama['pmdd_nama'];
            }else{
                $nama="UMUM";
            }
            $this->error_msg='Kode RM untuk Penjamin '.$nama.' sudah penuh. Hub Administrator IT anda !';
            return false;
        }else{//kode rm tidak full
            $rm=NULL;
            $check=false;
            while(!$check){
                $no_urut=NULL;
                $max=self::find()->where(['like','ps_kode',$kode_rm.'%',false])->max('ps_kode');
                //substr max rm
                if(!empty($max)){
                    $no_urut=substr($max,2);
                }
                $rm=$kode_rm.sprintf('%0'.Yii::$app->params['rm']['length'].'d',$no_urut+1);
                $check_count=self::find()->where(['ps_kode'=>$rm])->asArray()->limit(1)->one();
                if($check_count==NULL){
                    $check=true;
                }
            }
            if($rm!=NULL){
                $this->ps_kode=$rm;
                return true;
            }
            $this->error_msg="Terjadi kesalahan generate No. RM, Hub. Administrator IT anda !";
            return false;
        }
    }
    function savePasien()
    {
        if($this->ps_kode==NULL){
            if(!$this->generateRm()){
                return false;
            }
        }
        
        $log=[];
        if($this->scenario=="pasien_update"){
            $log['sebelum']=$this->attr();
        }
        $this->kunjungan=Registrasi::countKunjungan($this->ps_kode);
        $trans = self::getDb()->beginTransaction();
        try {
            $this->save(false);
            $anak=new PasienAnak();
            if(!$anak->saveAnakPasien($this)){
                $trans->rollBack();
                $this->error_msg="Error 1";
                return false;
            }
            $riw=new PasienPenanggung();
            if(!$riw->savePasienPenanggung($this)){
                $trans->rollBack();
                $this->error_msg="Error 4";
                return false;
            }
            if($this->scenario=="pasien_update"){
                $log['sesudah']=$this->attr();
            }
            $this->kunjungan++;
            Log::saveLog(($this->scenario=="pasien_baru"?"Buat":"Update")." Pasien",$log);
            $trans->commit();
            return true;
        } catch(\Exception $e) {
            $trans->rollBack();
            throw $e;
        } catch(\Throwable $e) {
            $trans->rollBack();
            throw $e;
        }
    }
    function searchPasien($req)
    {
        $id=$req->post('noidentitas');
        $type=$req->post('type');
        
        $id=trim($id);
        $model = Pasien::find()->with([
                'penanggung','anak',
                'kelurahan'=>function($q){
                    $q->select(["kel_kode as id","concat('<b>KEL:</b> ',kel_nama,' <b>KEC: </b>',kec_nama,', <b>KAB:</b> ',kab_nama,', <b>PROV:</b> ',prv_nama) as text"])
                    ->joinWith(['kecamatan'=>function($q){
                        $q->joinWith(['kabupaten'=>function($q){
                            $q->joinWith(['provinsi'],false);
                        }],false);
                    }],false);
                }
            ])->where([$type==1 ? 'ps_kode' : 'ps_no_identitas'=>$id])->asArray()->limit(1)->one();
        if($model!=NULL){
            $model['ps_tgl_lahir']=date('d-m-Y',strtotime($model['ps_tgl_lahir']));
            //custom jml anak n detail anak
            $model['anak_detail']['jml']=$model['ps_jml_anak'];
            unset($model['ps_jml_anak']);
            if(count($model['anak'])>0){
                $model['anak_detail']['data']=array_map(function($q){
                    return ['id'=>$q['ppa_id'],'nama'=>$q['ppa_nama'],'status'=>$q['ppa_status'],'tgl_lahir'=>date('d-m-Y',strtotime($q['ppa_tgl_lahir'])),'rm'=>$q['ppa_no_rekam_medis']];
                },$model['anak']);
                unset($model['anak']);
            }
            //custom penanggung
            if(count($model['penanggung'])>0){
                $model['penanggung']=array_map(function($q){
                    return ['debitur'=>$q['pen_pmd_kode'].'_'.$q['pen_pmdd_kode'],'nomor'=>$q['pen_no_kartu']];
                },$model['penanggung']);
            }
            
            //get latest rawatpoli/rawatinap
            $layanan=Layanan::searchLayanan($model['ps_kode']);
            $this->data=[
                'biodata'=>$model,
                'layanan'=>$layanan,
            ];
            return true;
        }
        $this->error_msg="Data pasien tidak ditemukan, silahkan periksa kembali No. RM atau No. Identitas pasien";
        return false;
    }
    function searchPasienRawatinap($noidentitas)
    {
        $result=[];
        $layanan=Layanan::activeRawatinap($noidentitas,false); //get data rawatinap
        if($layanan==NULL){
            $layanan=Layanan::latestRawatjalan($noidentitas); //get data rawatjalan
            if($layanan!=NULL){
                $result['status']=true;
                $result['msg']='Pendaftaran pasien berhasil ditemukan, <br>Unit : <b>'.$layanan['unit'].'</b><br>Tgl. Masuk : <b>'.$layanan['reg_tgl_masuk'].'</b><br>Debitur : <b>'.$layanan['debitur'].'</b>';
                $result['layanan']=$layanan;
            }else{
                $result['status']=false;
                $result['msg']='Pasien belum terdaftar di IGD/Poliklinik.<br>Silahkan daftarkan pasien ke IGD/Poliklinik terlebih dahulu';
            }
        }else{
            $result['status']=true;
            $result['msg']='Rawat Inap ditemukan, Nama Ruang : '.$layanan['unit'];
            $result['layanan']=$layanan;
        }
        if($result['status']){
            $biodata = Pasien::find()
                ->select(['ps_kode','ps_jenis_identitas','ps_no_identitas','ps_nama',
                'ps_tempat_lahir','ps_tgl_lahir','ps_jkel',
                'ps_goldar','agm_nama as ps_agama_id','suk_nama as ps_suku_id',
                'pdd_nama as ps_pendidikan_id','jur_nama as ps_jurusan_id','pkj_nama as ps_pekerjaan_id',
                'ps_tempat_kerja','ps_alamat_tempat_kerja','ps_penghasilan',
                'ps_no_telp','ps_alamat','CONCAT("kel : ",kel_nama,", kec : ",kec_nama,", kab : ",kab_nama,", prov : ",prv_nama) as ps_kelurahan_id',
                'ngr_nama as ps_kewarganegaraan_id','ps_marital_status','ps_kedudukan_keluarga','ps_nama_pasangan',
                'ps_anak_ke','ps_istri_ke','ps_jml_anak','ps_ayah_nama',
                'ps_ayah_no_rekam_medis','ps_ibu_nama','ps_ibu_no_rekam_medis','ps_kebiasaan','ps_riwayat_penyakit'])
                ->joinWith([
                    'negara',
                    'suku',
                    'pendidikan',
                    'jurusan',
                    'pekerjaan',
                    'agama',
                    'kelurahan'=>function($q){
                        $q->joinWith(['kecamatan'=>function($q){
                            $q->joinWith(['kabupaten'=>function($q){
                                $q->joinWith(['provinsi'],false);
                            }],false);
                        }],false);
                    }
                ],false)
                ->with([
                    'penanggung'=>function($q){
                        $q->select('pen_pasien_kode,pen_pmdd_kode,pmdd_nama,pen_no_kartu')->joinWith(['debiturdetail'],false);
                    },
                    'anak'
                ])
                ->where(['or',['like','ps_kode',$noidentitas],['like','ps_no_identitas',$noidentitas]])->notDeleted(self::$prefix)->asArray()->limit(1)->one();
            if($biodata!=NULL){
                if(count($biodata['penanggung'])>0){
                    $biodata['penanggung']=array_map(function($q){
                        return ['debitur'=>$q['pmdd_nama'],'nomor'=>$q['pen_no_kartu']];
                    },$biodata['penanggung']);
                }
                $biodata['ps_marital_status']=self::$status_kawin[$biodata['ps_marital_status']];
                $biodata['ps_jenis_identitas']=self::$jenis_identitas[$biodata['ps_jenis_identitas']];
                $biodata['ps_kedudukan_keluarga']=!empty($biodata['ps_kedudukan_keluarga']) ? self::$kddk[$biodata['ps_kedudukan_keluarga']] : NULL;
                $result['biodata']=$biodata;
            }else{
                $result['status']=false;
                $result['msg']="Data pasien tidak ditemukan, silahkan periksa kembali No. RM atau No. Identitas pasien";
            }
        }
        return $result;
    }
}
