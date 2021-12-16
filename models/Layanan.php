<?php
namespace app\models;
use Yii;
use app\widgets\AuthUser;
class Layanan extends \yii\db\ActiveRecord
{
    public $error_msg,$reg_data,$layanan_data;
    public $dokter,$status;
    public $no_kamar,$no_kasur,$kelas,$tarif;
    public static $igd=1,$rj=2,$ri=3;
    static $prefix="pl";
    public static function tableName()
    {
        return 'pendaftaran_layanan';
    }
    public function rules()
    {
        return [
            [['pl_reg_kode', 'pl_jenis_layanan', 'pl_tgl_masuk', 'pl_unit_kode'], 'required','on'=>'rj_create','message'=>'{attribute} harus diisi'],
            [['pl_reg_kode', 'pl_tgl_masuk', 'pl_unit_kode','pl_cmu_kode'], 'required','on'=>'ri_create','message'=>'{attribute} harus diisi'],

            [['pl_reg_kode', 'pl_jenis_layanan', 'pl_unit_kode'], 'required'],
            [['pl_reg_kode', 'pl_jenis_layanan', 'pl_unit_kode', 'pl_nomor_urut', 'pl_panggil_perawat', 'pl_dipanggil_perawat', 'pl_panggil_dokter', 'pl_dipanggil_dokter', 'pl_kamar_id', 'pl_unit_asal_kode', 'pl_unit_tujuan_kode', 'pl_cmu_kode', 'pl_ck_kode', 'pl_sk_kode', 'pl_created_by', 'pl_updated_by', 'pl_deleted_by'], 'integer'],
            [['pl_tgl_masuk', 'pl_tgl_keluar', 'pl_created_at', 'pl_updated_at', 'pl_deleted_at'], 'safe'],
            [['pl_keterangan'], 'string'],
            [['pl_kr_kode'], 'string', 'max' => 3],
        ];
    }
    public function attributeLabels()
    {
        return [
            'pl_id' => 'Pl ID',
            'pl_reg_kode' => 'No. Registrasi',
            'pl_jenis_layanan' => 'Jenis Layanan',
            'pl_tgl_masuk' => 'Tgl Masuk',
            'pl_tgl_keluar' => 'Tgl Keluar',
            'pl_unit_kode' => 'Ruang Rawatinap',
            'pl_nomor_urut' => 'Nomor Urut',
            'pl_panggil_perawat' => 'Pl Panggil Perawat',
            'pl_dipanggil_perawat' => 'Pl Dipanggil Perawat',
            'pl_panggil_dokter' => 'Pl Panggil Dokter',
            'pl_dipanggil_dokter' => 'Pl Dipanggil Dokter',
            'pl_kamar_id' => 'Pl Kamar ID',
            'pl_kr_kode' => 'Pl Kr Kode',
            'pl_unit_asal_kode' => 'Pl Unit Asal Kode',
            'pl_unit_tujuan_kode' => 'Pl Unit Tujuan Kode',
            'pl_cmu_kode' => 'Cara Masuk Unit',
            'pl_ck_kode' => 'Pl Ck Kode',
            'pl_sk_kode' => 'Pl Sk Kode',
            'pl_keterangan' => 'Pl Keterangan',
            'pl_created_at' => 'Pl Created At',
            'pl_created_by' => 'Pl Created By',
            'pl_updated_at' => 'Pl Updated At',
            'pl_updated_by' => 'Pl Updated By',
            'pl_deleted_at' => 'Pl Deleted At',
            'pl_deleted_by' => 'Pl Deleted By',

            'no_kamar'=>'No. Kamar',
            'no_kasur'=>'No. Kasur',
            'kelas'=>'Kelas',
            'tarif'=>'Tarif Per Hari'
        ];
    }
    function behaviors()
    {
        return [
            [
                'class'=>TrimBehavior::className(),
            ],
        ];
    }
    static function find()
    {
        return new BaseQuery(get_called_class());
    }
    function beforeSave($model)
    {
        if($this->isNewRecord){
            $this->pl_created_by=AuthUser::user()->id;
            $this->pl_created_at=date('Y-m-d H:i:s');
        }else{
            $this->pl_updated_by=AuthUser::user()->id;
            $this->pl_updated_at=date('Y-m-d H:i:s');
        }
        if($this->scenario=='ri_create'){
            $this->pl_tgl_masuk=date('Y-m-d H:i:s',strtotime($this->pl_tgl_masuk));
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
    function getKelas()
    {
        return $this->hasOne(KelasRawat::className(),['kr_kode'=>'pl_kr_kode']);
    }
    function getRegistrasi()
    {
        return $this->hasOne(Registrasi::className(),['reg_kode'=>'pl_reg_kode']);
    }
    function getUnit()
    {
        return $this->hasOne(Unit::className(),['unt_id'=>'pl_unit_kode']);
    }
    function getKamar()
    {
        return $this->hasOne(Kamar::className(),['kmr_id'=>'pl_kamar_id']);
    }
    static function isDipanggil($noreg,$tgl_masuk)
    {
        $model = self::find()->select('pl_panggil_perawat')->where(['pl_reg_kode'=>$noreg,"pl_tgl_masuk"=>date('Y-m-d H:i',strtotime($tgl_masuk))])->notDeleted(self::$prefix)->asArray()->limit(1)->one();
        if($model!=NULL){
            return $model['pl_panggil_perawat']==1 ? true : false;
        }
        return false;
    }
    static function searchLayanan($rm)
    {
        $igd=AuthUser::isIgd();
        $data=[];

        //kunjungan
        $kunjungan=Registrasi::countKunjungan($rm);
        $kunjungan = $kunjungan==0 ? ++$kunjungan : $kunjungan;
        
        //query layanan
        $query=self::find()->joinWith([
            'unit'=>function($q){
                $q->joinWith([
                    'kelompokunit'=>function($q){
                        $q->select('kul_unit_id,kul_type');
                    }
                ]);
            },
            'registrasi'=>function($q) use($rm){
                $q->joinWith(['pasien','kirimandetail','debiturdetail'])->where(['reg_pasien_kode'=>$rm]);
            }
        ])->notDeleted(self::$prefix)->orderBy(['pl_tgl_masuk'=>SORT_DESC])->orderBy(['pl_tgl_masuk'=>SORT_DESC])->asArray()->limit(1);
        $query_ri=clone $query;
        $rawatinap=$query_ri->andWhere("pl_jenis_layanan=3 and reg_tgl_keluar is null and pl_tgl_keluar is null and pl_unit_tujuan_kode is null")->one();
        if($rawatinap!=NULL){
            $data['status']=false;
            $data['msg']='Pasien : <b>'.$rawatinap['registrasi']['pasien']['ps_nama'].' ( '.$rm.' )</b><br>Terdaftar di Ruang <b>'.( $rawatinap['unit']!=NULL ? $rawatinap['unit']['unt_nama'] : NULL ).'</b><br>Tanggal Masuk : <b>'.date('d-m-Y H:i',strtotime($rawatinap['pl_tgl_masuk'])).'</b><br> Debitur : <b>'.$rawatinap['registrasi']['debiturdetail']['pmdd_nama'].'</b>';
        }else{
            $rawatjalan=$query->andWhere(['or',['pl_jenis_layanan'=>self::$rj],['pl_jenis_layanan'=>self::$igd]])->andWhere("DATE_FORMAT(pl_tgl_masuk,'%Y-%m-%d') = :tgl and pl_tgl_keluar is null",[':tgl'=>date('Y-m-d')])->one();
            if($rawatjalan!=NULL){
                if($igd){ //jika user loket igd
                    $tipe=array_column($rawatjalan['unit']['kelompokunit'],'kul_type');
                    if(in_array(self::$rj,$tipe)){//jika pasien rawatjalan
                        $data['status']=true;
                        $data['msg']='Pasien No. MR <b>'.$rm.'</b>,<br>Terdaftar ke Unit <b>'.$rawatjalan['unit']['unt_nama'].'</b><br> Tanggal <b>'.date('d-m-Y H:i',strtotime($rawatjalan['pl_tgl_masuk'])).'</b><br><b>Langsung ke MENU RAWATINAP jika pasien masuk RAWATINAP</b>';
                        $data['registrasi']['kunjungan']=$kunjungan;
                        $data['registrasi']['reg_pasien_kode']=$rm;
                        return $data;
                    }
                }else{ //jika user loket rj
                    $tipe=array_column($rawatjalan['unit']['kelompokunit'],'kul_type');
                    if(in_array(self::$igd,$tipe)){ //jika terdaftar igd
                        $data['status']=false;
                        $data['msg']='Pasien No. MR : <b>'.$rm.'</b><br>Terdaftar ke Unit : <b>'.$rawatjalan['unit']['unt_nama'].'</b><br>Tanggal : <b>'.date('d-m-Y H:i',strtotime($rawatjalan['pl_tgl_masuk'])).'</b><br>Debitur : <b>'.$rawatjalan['registrasi']['debiturdetail']['pmdd_nama'].'</b>';
                        return $data;
                    }
                }
                $data['status']=true;
                $data['registrasi']['kunjungan']=$kunjungan;
                $data['registrasi']['reg_pasien_kode']=$rawatjalan['registrasi']['reg_pasien_kode'];
                $data['registrasi']['reg_kode']=$rawatjalan['registrasi']['reg_kode'];
                $data['registrasi']['kiriman']=$rawatjalan['registrasi']['kirimandetail']['pmkd_pmkr_kode'];
                $data['registrasi']['reg_pmkd_kode']=$rawatjalan['registrasi']['reg_pmkd_kode'];
                $data['registrasi']['debitur']=$rawatjalan['registrasi']['debiturdetail']['pmdd_pmd_kode'];
                $data['registrasi']['reg_pmdd_kode']=$rawatjalan['registrasi']['reg_pmdd_kode'];
                $data['registrasi']['reg_no_sep']=$rawatjalan['registrasi']['reg_no_sep'];
                $data['registrasi']['unit']['kode']=$rawatjalan['pl_unit_kode'];
                $data['registrasi']['unit']['status']=self::isDipanggil($rawatjalan['pl_reg_kode'],$rawatjalan['pl_unit_kode'],$rawatjalan['pl_tgl_masuk']);
            }else{
                $data['status']=true;
                $data['registrasi']['reg_pasien_kode']=$rm;
                $data['registrasi']['kunjungan']=$kunjungan;
            }
        }
        return $data;     
    }
    function saveRawatjalan()
    {
        $jenis_layanan=Yii::$app->db->createCommand("SELECT kul_type FROM ".KelompokUnitLayanan::tableName()." WHERE kul_unit_id = :unit")->bindValues([':unit'=>$this->reg_data->unit])->queryOne();
        $mlayanan = self::find()->joinWith([
            'registrasi'=>function($q){
                $q->where(['reg_pasien_kode'=>$this->reg_data->reg_pasien_kode]);
            }
        ],false)->andWhere(['pl_reg_kode'=>$this->reg_data->reg_kode,"DATE_FORMAT(pl_tgl_masuk,'%Y-%m-%d %H:%i')"=>date('Y-m-d H:i',strtotime($this->reg_data->reg_tgl_masuk))])->limit(1)->one();
        $model = $mlayanan!=NULL ? $mlayanan : new Layanan();
        $model->scenario="rj_create";
        $model->pl_reg_kode=$this->reg_data->reg_kode;
        $model->pl_jenis_layanan=$jenis_layanan['kul_type'];
        $model->pl_tgl_masuk=$this->reg_data->reg_tgl_masuk;
        $model->pl_unit_kode=$this->setKodeUnit();
        if($model->isNewRecord){
            $model->pl_nomor_urut=$this->generateNoAntrian();
        }
        if($model->validate()){
            if($model->save(false)){
                $this->layanan_data=$model;
                return true;
            }else{
                $this->error_msg="Pelayanan gagal disimpan";
                return false;
            }
        }else{
            $this->error_msg=$model->errors;
            return false;
        }
    }
    function setKodeUnit()
    {
        if(!$this->isNewRecord){
            if(!self::isDipanggil($this->reg_data->reg_kode,$this->reg_data->reg_tgl_masuk)){
                if($this->reg_data->unit!=$this->pl_unit_kode){
                    $this->pl_unit_kode=$this->reg_data->unit;
                    $this->nomor_urut=$this->generateNoAntrian();
                }
            }
        }
        return $this->reg_data->unit;
    }
    function generateNoAntrian()
    {
        $find=false;
        $max_now=self::find()->where(['pl_unit_kode'=>$this->reg_data->unit,"DATE_FORMAT(pl_tgl_masuk,'%Y-%m-%d')"=>date('Y-m-d')])->notDeleted(self::$prefix)->max('pl_nomor_urut');
        $max= !empty($max_now) ? $max_now : 1;
        while(!$find){
            $count=self::find()->where(['pl_nomor_urut'=>$max,'pl_unit_kode'=>$this->reg_data->unit,"DATE_FORMAT(pl_tgl_masuk,'%Y-%m-%d')"=>date('Y-m-d')])->notDeleted(self::$prefix)->count();
            if($count<1){
                $find=true;
            }else{
                $max++;
            }
        }
        return $max;
    }
    static function getNowLayanan($rm,$jenis=[])
    {
        if(count($jenis)<1){
            $jenis=[self::$igd,self::$rj];
        }
        //jenis 1 rawatinap,2 rawatjalan
        $model= self::find()->joinWith([
            'unit',
            'registrasi'=>function($q) use($rm){
                $q->joinWith(['pasien'],false)->where(['reg_pasien_kode'=>$rm]);
            }
        ],false)->notDeleted(self::$prefix)->andWhere(['in','pl_jenis_layanan',$jenis])->andWhere(["DATE_FORMAT(pl_tgl_masuk,'%Y-%m-%d')"=>date('Y-m-d')])
        ->select('ps_kode,ps_nama,ps_tempat_lahir,ps_tgl_lahir,reg_kode,pl_tgl_masuk,unt_nama as unit,pl_panggil_perawat as panggil_perawat,pl_unit_asal_kode')
        ->orderBy(['pl_tgl_masuk'=>SORT_DESC])->asArray()->limit(1)->one();
        if($model!=NULL){
            $model['ps_tgl_lahir']=date('d-m-Y',strtotime($model['ps_tgl_lahir']));
            $model['pl_tgl_masuk']=date('d-m-Y H:i:s',strtotime($model['pl_tgl_masuk']));
            return $model;
        }
        return false;
    }
    static function activeRawatinap($noidentitas,$checkout)
    {
        $query=self::find()->joinWith([
            'unit',
            'registrasi'=>function($q){
                $q->joinWith(['pasien'],false);
            },
            'kamar'=>function($q){
                $q->joinWith(['kelas','tarif'],false);
            }
        ],false)->where(['pl_jenis_layanan'=>self::$ri])->andWhere(['or',['ps_kode'=>$noidentitas],['ps_no_identitas'=>$noidentitas]]);
        if($checkout){
            $query->andWhere('pl_tgl_keluar is not null');
        }else{
            $query->andWhere('pl_tgl_keluar is null');
        }
        $query->select(['pl_id','pl_reg_kode','pl_tgl_masuk','pl_unit_kode','pl_kamar_id','unt_nama as unit','pl_kr_kode','kr_nama as kelas','kmr_no_kamar as no_kamar','kmr_no_kasur as no_kasur','tkr_biaya as tarif','pl_cmu_kode']);
        $result=$query->notDeleted(self::$prefix)->orderBy(['pl_tgl_masuk'=>SORT_DESC])->asArray()->limit(1)->one();
        if($result!=NULL){
            $result['pl_tgl_masuk']=date('d-m-Y H:i:s',strtotime($result['pl_tgl_masuk']));
            $result['dokter']=PjpRi::getListDpjp($result['pl_reg_kode']);
            return $result;
        }
        return NULL;
    }
    static function latestRawatjalan($noidentitas)
    {
        $result= self::find()->joinWith([
            'unit',
            'registrasi'=>function($q){
                $q->joinWith(['pasien','debiturdetail','kirimandetail'],false);
            }
        ],false)->where(['or',['reg_pasien_kode'=>$noidentitas],['ps_no_identitas'=>$noidentitas]])
        ->andWhere(['or',['pl_jenis_layanan'=>self::$igd],['pl_jenis_layanan'=>self::$rj]])->andWhere(["between","DATE_FORMAT(pl_tgl_masuk,'%Y-%m-%d')",date('Y-m-d',strtotime("-1 days")),date('Y-m-d')])->notDeleted(self::$prefix)
        ->select(['pl_reg_kode','reg_kode','reg_tgl_masuk','unt_nama as unit','pmdd_nama as debitur'])
        ->orderBy(['pl_tgl_masuk'=>SORT_DESC])->asArray()->limit(1)->one();
        if($result!=NULL){
            $result['reg_tgl_masuk']=date('d-m-Y H:i:s',strtotime($result['reg_tgl_masuk']));
            $result['dokter']=PjpRi::getListDpjp($result['reg_kode']);
        }
        return $result;
    }
    function saveRawatinap()
    {
        $transaction = self::getDb()->beginTransaction();
        try {
            $this->save(false);
            $dpjp = new PjpRi();
            $dpjp->layanan_data=$this;
            if(!$dpjp->saveDpjpRi()){
                $transaction->rollBack();
                return false;
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
    function batalRawatinap()
    {
        $transaction = self::getDb()->beginTransaction();
        try {
            $this->pl_deleted_at=date('Y-m-d H:i:s');
            $this->pl_deleted_by=AuthUser::user()->id;
            $this->save(false);
            $dpjp = new PjpRi();
            $dpjp->layanan_data=$this;
            if(!$dpjp->deleteDpjpRi()){
                $transaction->rollBack();
                return false;
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