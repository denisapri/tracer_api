<?php
namespace app\models;
use Yii;
use app\widgets\AuthUser;
class Sep extends \yii\db\ActiveRecord
{
    public $error_msg,$keluhan,$scenario_name;
    public $penjamin_lakalantas;
    public $reg_data,$sep_data;
    static $prefix="sep";
    public static function tableName()
    {
        return 'pendaftaran_sep';
    }
    public function rules()
    {
        return [
            [['sep_pasien_kode','sep_no_rujukan','sep_tgl_sep','sep_tgl_rujukan', 'sep_asal_rujukan_kode', 'sep_jenis_pelayanan','sep_tingkat_faskes','sep_diagnosa_kode'], 'required','on'=>['create_rj','update_rj'],'message'=>'{attribute} harus diisi'],
            [['sep_pasien_kode','sep_no_rujukan','sep_tgl_sep','sep_tgl_rujukan', 'sep_asal_rujukan_kode', 'sep_jenis_pelayanan','sep_tingkat_faskes','sep_diagnosa_kode'], 'required','on'=>['create_ri','update_ri'],'message'=>'{attribute} harus diisi'],
            [['sep_no_sep','sep_tgl_checkout_sep'], 'required','on'=>'checkout_sep','message'=>'{attribute} harus diisi'],
            
            [['sep_tgl_rujukan', 'sep_tgl_sep','sep_tgl_checkout_sep', 'sep_laka_lantas_tgl_kejadian', 'sep_created_at', 'sep_updated_at', 'sep_deleted_at'], 'safe'],
            [['sep_tingkat_faskes', 'sep_jenis_pelayanan', 'sep_hak_kelas', 'sep_is_kontrol_post_ri', 'sep_is_poli_eksekutif', 'sep_is_bridging', 'sep_is_cob', 'sep_is_katarak', 'sep_is_duplikat', 'sep_is_laka_lantas', 'sep_laka_lantas_suplesi', 'sep_created_by', 'sep_updated_by', 'sep_deleted_by'], 'integer'],
            [['sep_laka_lantas_ket','sep_kelas_rawat'], 'string'],
            [['sep_reg_kode', 'sep_pasien_kode', 'sep_asal_rujukan_kode', 'sep_poli_kode', 'sep_diagnosa_kode', 'sep_dpjp_kode'], 'string', 'max' => 100],
            [['sep_no_sep', 'sep_no_rujukan', 'sep_no_kartu', 'sep_no_telp', 'sep_laka_lantas_penjamin'], 'string', 'max' => 50],
            [['sep_asal_rujukan_nama', 'sep_poli_nama', 'sep_diagnosa_nama', 'sep_dpjp_nama', 'sep_skdp_no_surat', 'sep_catatan', 'sep_laka_lantas_no_suplesi', 'sep_laka_lantas_prov_nama', 'sep_laka_lantas_kab_nama', 'sep_laka_lantas_kec_nama'], 'string', 'max' => 255],
            [['sep_laka_lantas_prov_kode', 'sep_laka_lantas_kab_kode', 'sep_laka_lantas_kec_kode'], 'string', 'max' => 5],
        ];
    }
    public function attributeLabels()
    {
        return [
            'sep_id' => 'ID',
            'sep_reg_kode' => 'No. Registrasi',
            'sep_pasien_kode' => 'No. Rekam Medis',
            'sep_no_sep' => 'No. Sep',
            'sep_no_rujukan' => 'No. Rujukan',
            'sep_no_kartu' => 'No. Kartu',
            'sep_tgl_rujukan' => 'Tgl. Rujukan',
            'sep_tgl_sep' => 'Tgl. Sep',
            'sep_tgl_checkout_sep'=>'Tgl. Checkout SEP',
            'sep_asal_rujukan_kode' => 'Asal Rujukan',
            'sep_asal_rujukan_nama' => 'Sep Asal Rujukan Nama',
            'sep_tingkat_faskes' => 'Tingkat Faskes',
            'sep_jenis_pelayanan' => 'Jenis Pelayanan',
            'sep_hak_kelas' => 'Hak Kelas',
            'sep_kelas_rawat' => 'Kelas Rawat',
            'sep_poli_kode' => 'Poliklinik',
            'sep_poli_nama' => 'Nama Poliklinik',
            'sep_diagnosa_kode' => 'Diagnosa',
            'sep_diagnosa_nama' => 'Nama Diagnosa',
            'sep_dpjp_kode' => 'Nama DPJP',
            'sep_dpjp_nama' => 'Nama Dpjp',
            'sep_skdp_no_surat' => 'SPRI/No. Surat Kontrol',
            'sep_no_telp' => 'No. Telp',
            'sep_catatan' => 'Catatan',
            'sep_is_kontrol_post_ri' => 'Kontrol Post Rawatinap ?',
            'sep_is_poli_eksekutif' => 'Poli Eksekutif ?',
            'sep_is_bridging' => 'Sep Is Bridging',
            'sep_is_cob' => 'Peserta Cob ?',
            'sep_is_katarak' => 'Peserta Katarak',
            'sep_is_duplikat' => 'Sep Is Duplikat',
            'sep_is_laka_lantas' => 'Peserta Laka Lantas ?',
            'sep_laka_lantas_penjamin' => 'Laka Lantas Penjamin',
            'sep_laka_lantas_tgl_kejadian' => 'Tgl. Kejadian Laka Lantas',
            'sep_laka_lantas_ket' => 'Keterangan',
            'sep_laka_lantas_suplesi' => 'Peserta Suplesi ?',
            'sep_laka_lantas_no_suplesi' => 'No. Suplesi',
            'sep_laka_lantas_prov_kode' => 'Provinsi',
            'sep_laka_lantas_prov_nama' => 'Sep Laka Lantas Prov Nama',
            'sep_laka_lantas_kab_kode' => 'Kabupaten',
            'sep_laka_lantas_kab_nama' => 'Sep Laka Lantas Kab Nama',
            'sep_laka_lantas_kec_kode' => 'Kecamatan',
            'sep_laka_lantas_kec_nama' => 'Sep Laka Lantas Kec Nama',
            'sep_created_at' => 'Sep Created At',
            'sep_created_by' => 'Sep Created By',
            'sep_updated_at' => 'Sep Updated At',
            'sep_updated_by' => 'Sep Updated By',
            'sep_deleted_at' => 'Sep Deleted At',
            'sep_deleted_by' => 'Sep Deleted By',
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
    function init()
    {
        $this->penjamin_lakalantas=[1=>'PT. Jasa Raharja',2=>'BPJS Ketenagakerjaan',3=>'PT. TASPEN',4=>'PT. ASABRI'];
        $this->scenario_name=['update_rj'=>'Update SEP Rawat Jalan','create_rj'=>'Create SEP Rawat Jalan','create_ri'=>'Create SEP Rawat Inap','update_ri'=>'Update SEP Rawat Inap'];
    }
    function beforeValidate()
    {
        if($this->sep_jenis_pelayanan==2){
            if(trim($this->sep_kelas_rawat)=='-'){
                $this->sep_kelas_rawat='3';
            }
        }
        return parent::beforeValidate();
    }
    function beforeSave($model)
    {
        if($this->isNewRecord){
            $this->sep_created_by=AuthUser::user()->id;
            $this->sep_created_at=date('Y-m-d H:i:s');
        }else{
            $this->sep_updated_by=AuthUser::user()->id;
            $this->sep_updated_at=date('Y-m-d H:i:s');
        }
        if($this->sep_is_laka_lantas==1){
            if(count($this->sep_laka_lantas_penjamin)>0){
                $this->sep_laka_lantas_penjamin=implode(',',$this->sep_laka_lantas_penjamin);
            }
            $this->sep_laka_lantas_tgl_kejadian=$this->sep_laka_lantas_tgl_kejadian!=NULL ? date('Y-m-d',strtotime($this->sep_laka_lantas_tgl_kejadian)) : NULL;
        }
        $this->sep_tgl_rujukan=date('Y-m-d',strtotime($this->sep_tgl_rujukan));
        $this->sep_tgl_sep=date('Y-m-d',strtotime($this->sep_tgl_sep));
        return parent::beforeSave($model);
    }
    public function getRegistrasi()
    {
        return $this->hasOne(Registrasi::className(), ['reg_kode' => 'sep_reg_kode']);
    }
    function getPeserta($nomor,$jenis=1)
    {
        $q = new Bridging();
        if($q->queryGetPeserta($nomor,$jenis)->exec()){
            $dp = $q->getResponse();
            $result=[
                'status'=>true,
                'msg'=>'Data peserta berhasil ditemukan',
                'peserta'=>[
                    'nokartu'=>$dp->peserta->noKartu,
                    'nik'=>$dp->peserta->nik,
                    'norm'=>$dp->peserta->mr->noMR,
                    'nama'=>$dp->peserta->nama,
                    'jkel'=>$dp->peserta->sex=='L'?'Laki-Laki':'Perempuan',
                    'tgl_lahir'=>date('d-m-Y',strtotime($dp->peserta->tglLahir)),
                    'no_telp'=>$dp->peserta->mr->noTelepon,
                    'faskes1'=>$dp->peserta->provUmum->kdProvider.' - '.$dp->peserta->provUmum->nmProvider,
                    'kelas'=>$dp->peserta->hakKelas->keterangan,
                    'kelas_kode'=>$dp->peserta->hakKelas->kode,
                    'status'=>$dp->peserta->jenisPeserta->keterangan,
                    'aktif'=>$dp->peserta->statusPeserta->keterangan,
                    'prolanis_prb'=>$dp->peserta->informasi->prolanisPRB,
                    'dinsos'=>$dp->peserta->informasi->dinsos,
                ]
            ];
        }else{
            $result=['status'=>false,'msg'=>$q->errorMessage];
        }
        return $result;
    }
    function getRujukan($norujukan)
    {
        $norujukan=trim($norujukan);
        $q = new Bridging();
        if(!$q->queryGetRujukan($norujukan,1,1)->exec()){
            $q->queryGetRujukan($norujukan,1,2)->exec();
        }
        return $q->getResponse();
    }
    function getPoliklinik($poli)
    {
        $poli=trim($poli);
        $data=null;
        $q = new Bridging();
        if($q->queryGetListPoli($poli)->exec()){
            $tmp=$q->getResponse();
            $result=array_filter($tmp->poli,function($q) use($poli){
                return trim($q->kode)==$poli || trim($q->nama)==$poli;
            });
            if(count($result)>0){
                foreach($result as $dt){
                    $data=['id'=>$dt->kode,'text'=>$dt->nama];
                    break;
                }
            }
        }
        return $data;
    }
    function getDiagnosa($diagnosa)
    {
        $data=NULL;
        $q = new Bridging();
        if($q->queryGetListDiagnosa(trim($diagnosa))->exec()){
            $diagnosa_tmp=$q->getResponse();
            foreach($diagnosa_tmp->diagnosa as $dt){
                $x=explode(' - ',$dt->nama);
                if(trim($x[1])==trim($diagnosa)){
                    $data=['id'=>$dt->kode,'text'=>$dt->nama];
                    break;
                }
            }
        }
        return $data;
    }
    function getFaskes($kode)
    {
        $q = new Bridging();
        $tingkat_faskes=1;
        if(!$q->queryGetListFaskes(trim($kode),$tingkat_faskes)->exec()){
            $tingkat_faskes=2;
            if(!$q->queryGetListFaskes(trim($kode),$tingkat_faskes)->exec()){
                return NULL;
            }
        }
        return ['tingkat_faskes'=>$tingkat_faskes,'faskes'=>$q->getResponse()->faskes[0]];
    }
    function getDpjp($poli,$jenis_pelayanan,$no_rujukan=NULL,$no_kartu=NULL)
    {
        $html='';
        $q = new Bridging();
        
        $r=explode('|#|',$poli);
        $poli=count($r)>1 ? $r[1] : $r[0];
        
        //pencarian poli, utk kontrol 
        if($no_kartu!=NULL && $no_rujukan!=NULL){
            $q->queryHistoryPelayananPeserta($no_kartu,(date('Y')-1).'-'.date('m-d'),date('Y-m-d'))->exec();
            $history=$q->getResponse();
            if($q->errorCode==NULL){
                $tmp=array_filter($history->histori,function($e) use($no_rujukan){
                    return $e->noRujukan==$no_rujukan;
                });
                if(count($tmp)>0){
                    foreach($tmp as $key => $h){
                        if($h->poli!="INSTALASI GAWAT DARURAT" && !empty($h->poli)){
                            $poli=$h->poli;
                            break;
                        }
                    }
                }
            }
        }
        //get kode poli
        if($q->queryGetListPoli(trim($poli))->exec()){
            $poli_result=$q->getResponse();
            $poli_tmp=$poli_result->poli;
            
            $poli_tmp=array_filter($poli_result->poli,function($e) use($poli){
                return trim($e->nama) == trim($poli);
            });
            if(count($poli_tmp)>0){
                foreach($poli_tmp as $p);
                $poli=$p->kode;
            }
        }
        if($q->queryGetDokterDpjp(trim($poli),$jenis_pelayanan,date('Y-m-d'))->exec()){
            $dp=$q->getResponse();
            if($dp){
                if(count($dp->list)>0){
                    $html='<option value="">- pilh DPJP -</option>';
                    foreach($dp->list as $l){
                        $html.='<option value="'.$l->kode.'|#|'.$l->nama.'">'.$l->nama.'</option>';
                    }
                }else{
                    $html='<option value="">- DPJP Tidak Tersedia -</option>';
                }
            }else{
                $html='<option value="">- DPJP Tidak Tersedia -</option>';
            }
        }
        return $html;
    }
    function setDataSep()
    {
        if($this->sep_jenis_pelayanan==1){
            $this->sep_kelas_rawat=$this->sep_hak_kelas;
        }
        //asal rujukan
        if($this->sep_asal_rujukan_kode!=NULL){
            $asal_rujukan=explode('|#|',$this->sep_asal_rujukan_kode);
            $this->sep_asal_rujukan_kode=$asal_rujukan[0];
            $this->sep_asal_rujukan_nama=$asal_rujukan[1];
        }

        //diagnosa
        if($this->sep_diagnosa_kode!=NULL){
            $diagnosa=explode('|#|',$this->sep_diagnosa_kode);
            $this->sep_diagnosa_kode=$diagnosa[0];
            $this->sep_diagnosa_nama=$diagnosa[1];
        }

        //poli
        if($this->sep_poli_kode!=NULL){
            $poli=explode('|#|',$this->sep_poli_kode);
            $this->sep_poli_kode=$poli[0];
            $this->sep_poli_nama=$poli[1];
        }

        //dpjp
        if($this->sep_dpjp_kode!=NULL){
            $dpjp=explode('|#|',$this->sep_dpjp_kode);
            $this->sep_dpjp_kode=$dpjp[0];
            $this->sep_dpjp_nama=$dpjp[1];
        }

        if($this->sep_is_laka_lantas==1){
            //penjamin laka lantas
            if($this->sep_laka_lantas_penjamin!=NULL){
                $this->sep_laka_lantas_penjamin=implode(',',$this->sep_laka_lantas_penjamin);
            }
        }
    }
    function saveSep($req)
    {
        $transaction = self::getDb()->beginTransaction();
        try {
            $this->setDataSep();
            $this->save(false);
            if(!$this->bridgingSep()){
                $transaction->rollBack();
                return false;
            }
            $up=self::find()->where(['sep_id'=>$this->sep_id])->limit(1)->one();
            $up->sep_no_sep=$this->sep_no_sep;
            $up->save(false);
            Log::saveLog($this->scenario_name[$this->scenario],$this->attr());
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
    function bridgingSep()
    {
        $br = new Bridging();
        $tsep=[
            'klsRawat'=>(string) $this->sep_kelas_rawat,
            'noMR'=>(string) $this->sep_pasien_kode,
            'rujukan'=>[
                "asalRujukan" =>(string) $this->sep_tingkat_faskes,
                "tglRujukan" =>(string) date('Y-m-d',strtotime($this->sep_tgl_rujukan)),
                "noRujukan" =>(string) $this->sep_no_rujukan,
                "ppkRujukan" =>(string) $this->sep_asal_rujukan_kode
            ],
            'catatan'=>(string) $this->sep_catatan,
            'diagAwal'=>(string) $this->sep_diagnosa_kode,
            'poli'=>[
                'tujuan'=>(string) $this->sep_poli_kode,
                'eksekutif'=>(string) $this->sep_is_poli_eksekutif
            ],
            'cob'=>[
                'cob'=>(string) $this->sep_is_cob,
            ],
            'katarak'=>[
                'katarak'=>(string) $this->sep_is_katarak,
            ],
            'jaminan'=>[
                'lakaLantas'=>(string) $this->sep_is_laka_lantas,
                'penjamin'=>[
                    'penjamin'=>(string) $this->sep_laka_lantas_penjamin,
                    'tglKejadian'=>(string) $this->sep_laka_lantas_tgl_kejadian!=NULL ? date('Y-m-d',strtotime($this->sep_laka_lantas_tgl_kejadian)) : '',
                    'keterangan'=>(string) $this->sep_laka_lantas_ket,
                    'suplesi'=>[
                        'suplesi'=>(string) $this->sep_laka_lantas_suplesi,
                        'noSepSuplesi'=>(string)$this->sep_laka_lantas_no_suplesi,
                        'lokasiLaka'=>[
                            'kdPropinsi'=>(string) $this->sep_laka_lantas_prov_kode,
                            'kdKabupaten'=>(string) $this->sep_laka_lantas_kab_kode,
                            'kdKecamatan'=>(string) $this->sep_laka_lantas_kec_kode,
                        ]
                    ]
                ]
            ],
            'skdp'=>[
                'noSurat'=>(string) $this->sep_skdp_no_surat,
                'kodeDPJP'=>(string) $this->sep_dpjp_kode
            ],
            'noTelp'=>(string) $this->sep_no_telp,
            'user'=>AuthUser::user()->username,
        ];
        
        if(empty($this->sep_no_sep)){
            $tsep['noKartu']=(string) $this->sep_no_kartu;
            $tsep['tglSep']=(string) date('Y-m-d',strtotime($this->sep_tgl_sep));
            $tsep['ppkPelayanan']=(string) Yii::$app->params['bpjs']['app']['kode_rsud'];
            $tsep['jnsPelayanan']=(string) $this->sep_jenis_pelayanan;
        }else{
            $tsep['noSep']=$this->sep_no_sep;
        }
        $data=[
            'request'=>[
                't_sep'=>$tsep
            ]
        ];
        if(empty($this->sep_no_sep)){
            if($br->queryInsertSEP($data)->exec()){
                $sep=$br->getResponse();
                $this->sep_no_sep=$sep->sep->noSep;
                return true;
            }
        }else{
            if($br->queryUpdateSEP($data)->exec()){
                $sep=$br->getResponse();
                $this->sep_no_sep=$sep;
                return true;
            }
        }
        $this->error_msg="BPJS : ".$br->errorMessage;
        return false;
    }
    function deleteSep()
    {
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            if(!$this->isNewRecord){
                $this->sep_deleted_at=date('Y-m-d H:i:s');
                $this->sep_deleted_by=AuthUser::user()->id;
            }
            $br = new Bridging();
            if(!$br->queryHapusSEP([
                'request'=>[
                    't_sep'=>[
                        'noSep'=>(string) $this->sep_no_sep,
                        'user'=>(string) AuthUser::user()->id,
                    ]
                ]
            ])->exec()){
                $this->error_msg='BPJS : '.$br->errorMessage;
                $transaction->rollBack();
                return false;
            }
            if(!$this->isNewRecord){
                $this->save(false);
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
    function insertSep()
    {
        if($this->reg_data->reg_pmdd_kode==Yii::$app->params['bpjs']['app']['id']){
            if(!empty($this->reg_data->reg_no_sep)){
                $sep = Sep::find()->where(['sep_pasien_kode'=>$this->reg_data->reg_pasien_kode,'sep_no_sep'=>$this->reg_data->reg_no_sep])->limit(1)->one();
                if($sep!=NULL){ //jika sep tersimpan, update noreg
                    $sep->sep_reg_kode=$this->reg_data->reg_kode;
                    if(!$sep->save(false)){
                        return false;
                    }
                    $this->sep_data=$sep;
                }else{ //if no. sep using non bridging, get data from bpjs
                    $count=Sep::find()->where(['sep_no_sep'=>$this->reg_data->reg_no_sep])->count();
                    $duplikat=0;
                    if($count>0){
                        $duplikat=1;
                    }
                    $q = new Bridging();
                    if($br=$q->querySearchSEP($this->reg_data->reg_no_sep)->exec()){
                        $ds = $q->getResponse();
                        //data rujukan
                        $q = new Bridging();
                        if(!$q->queryGetRujukan($ds->noRujukan,1,1)->exec()){
                            $q->queryGetRujukan($ds->noRujukan,1,2)->exec();
                        }
                        $rujukan=$q->getResponse();

                        //poli
                        $poli=$this->getPoliklinik($ds->poli);
                        //diagnosa
                        $diagnosa=$this->getDiagnosa($ds->diagnosa);
                        //tingkat faskes
                        $faskes=$rujukan!=NULL ? $this->getFaskes($rujukan->rujukan->provPerujuk->kode) : NULL;
                        $data=[
                            'sep_reg_kode' =>$this->reg_data->reg_kode,
                            'sep_no_sep' => $ds->noSep,
                            'sep_no_rujukan'=>$ds->noRujukan,
                            'sep_no_kartu'=>$ds->peserta->noKartu,
                            'sep_tgl_rujukan'=>$rujukan!=NULL ? date('Y-m-d H:i:s',strtotime($rujukan->rujukan->tglKunjungan)) : NULL,
                            'sep_tgl_sep'=>date('Y-m-d H:i:s',strtotime($ds->tglSep)),
                            'sep_asal_rujukan_kode'=>$faskes!=NULL ? $faskes['faskes']->kode : NULL,
                            'sep_jenis_pelayanan'=>$ds->jnsPelayanan=='Rawat Inap' ? 1 : 2,
                            'sep_hak_kelas'=>$rujukan!=NULL ? $rujukan->rujukan->pelayanan->kode : NULL,
                            'sep_poli_kode'=>$poli['id'],
                            'sep_poli_nama'=>$poli['text'],
                            'sep_diagnosa_kode'=>$diagnosa['id'],
                            'sep_diagnosa_nama'=>$diagnosa['text'],
                            'sep_is_bridging'=>0,
                            'sep_created_by'=>AuthUser::user()->id,
                            'sep_created_at'=>date('Y-m-d H:i:s'),
                            'sep_pasien_kode'=>$ds->peserta->noMr,
                            'sep_tingkat_faskes'=>$faskes!=NULL ? $faskes['tingkat_faskes'] : NULL,
                            'sep_asal_rujukan_nama'=>$faskes!=NULL ? $faskes['faskes']->nama : NULL,
                            'sep_catatan'=>$ds->catatan,
                            'sep_no_telp'=>$rujukan!=NULL ? $rujukan->rujukan->peserta->mr->noTelepon : NULL,
                            'sep_kelas_rawat'=>$ds->kelasRawat,
                            'sep_is_duplikat'=>$duplikat
                        ];
                        Yii::$app->db->createCommand()->insert(self::tableName(), $data)->execute();
                    }
                }
            }
        }
        return true;
    }
    function checkoutSep()
    {
        $data=[
            'request'=>[
                't_sep'=>[
                    'noSep'=>(string) $this->sep_no_sep,
                    'tglPulang'=>(string) date('Y-m-d H:i:s', strtotime($this->sep_tgl_checkout_sep)),
                    'user'=>(string) AuthUser::user()->id,
                ]
            ]
        ];

        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            
            $br = new Bridging();
            if(!$br->queryCheckOutSEP($data)->exec()){
                $this->error_msg=$br->errorMessage;
                $transaction->rollBack();
                return false;
            }

            if(!$this->isNewRecord){
                $this->save(false);
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
}