<?php
namespace app\models;
use Yii;
use app\widgets\AuthUser;
class Layanan_ extends \yii\db\ActiveRecord
{
    public $error_msg,$reg_data,$layanan_data;
    public $dokter,$status;
    public $no_kamar,$no_kasur,$kelas,$tarif;
    public static $rj=[1,2],$ri=[3];
    public static function tableName()
    {
        return 'pendaftaran.layanan';
    }
    public function rules()
    {
        return [
            [['registrasi_kode', 'jenis_layanan', 'tgl_masuk', 'unit_kode'], 'required','on'=>'rj_create','message'=>'{attribute} harus diisi'],
            [['registrasi_kode', 'tgl_masuk', 'unit_kode','cara_masuk_unit_kode'], 'required','on'=>'ri_create','message'=>'{attribute} harus diisi'],
            [['jenis_layanan', 'nomor_urut', 'panggil_perawat','created_by', 'updated_by'], 'default', 'value' => null],
            [['jenis_layanan', 'nomor_urut', 'panggil_perawat', 'created_by', 'updated_by'], 'integer'],
            [['tgl_masuk', 'tgl_keluar', 'created_at', 'updated_at','deleted_at'], 'safe'],
            [['keterangan'], 'string'],
            [['registrasi_kode', 'unit_kode', 'kamar_id', 'kelas_rawat_kode', 'unit_asal_kode', 'unit_tujuan_kode', 'cara_masuk_unit_kode', 'cara_keluar_kode', 'status_keluar_kode'], 'string', 'max' => 10],
            [['id'], 'unique'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'Kode',
            'registrasi_kode' => 'No. Registrasi',
            'jenis_layanan' => 'Jenis Layanan',
            'tgl_masuk' => 'Tgl Masuk',
            'tgl_keluar' => 'Tgl Keluar',
            'unit_kode' => 'Pilih Unit',
            'nomor_urut' => 'Nomor Urut',
            'panggil_perawat' => 'Dipanggil Perawat',
            'kamar_id' => 'Bed Kode',
            'kelas_rawat_kode' => 'Kelas',
            'unit_asal_kode' => 'Unit Asal Kode',
            'unit_tujuan_kode' => 'Unit Tujuan Kode',
            'cara_masuk_unit_kode' => 'Cara Masuk Unit/Ruangan',
            'cara_keluar_kode' => 'Cara Keluar Kode',
            'status_keluar_kode' => 'Status Keluar Kode',
            'keterangan' => 'Keterangan',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',

            'no_kamar'=>'No. Kamar',
            'no_kasur'=>'No. Kasur',
            'kelas'=>'Kelas',
            'tarif'=>'Tarif Per Hari'
        ];
    }
    function beforeSave($model)
    {
        if($this->isNewRecord){
            $this->created_by=AuthUser::user()->id;
            $this->created_at=date('Y-m-d H:i:s');
        }else{
            $this->updated_by=AuthUser::user()->id;
            $this->updated_at=date('Y-m-d H:i:s');
        }
        return parent::beforeSave($model);
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
    function attr()
    {
        $data=[];
        foreach($this->attributeLabels() as $key => $val){
            $data[$val]=$this->{$key};
        }
        return $data;
    }
    function getRegistrasi()
    {
        return $this->hasOne(Registrasi::className(),['kode'=>'registrasi_kode']);
    }
    function getUnit()
    {
        return $this->hasOne(UnitPenempatan::className(),['kode'=>'unit_kode']);
    }
    function getKamar()
    {
        return $this->hasOne(Kamar::className(),['id'=>'kamar_id']);
    }
    static function isDipanggil($noreg,$tgl_masuk)
    {
        $model = self::find()->select('panggil_perawat')->where(['registrasi_kode'=>$noreg,"TO_CHAR(tgl_masuk :: TIMESTAMP,'YYYY-MM-DD HH24:MI')"=>date('Y-m-d H:i',strtotime($tgl_masuk))])->andWhere('deleted_at is null')->asArray()->limit(1)->one();
        if($model!=NULL){
            return $model['panggil_perawat']==1 ? true : false;
        }
        return false;
    }
    function saveRawatjalan()
    {
        $jenis_layanan=Yii::$app->db->createCommand("SELECT type FROM ".KelompokUnitLayanan::tableName()." WHERE unit_id = :unit")->bindValues([':unit'=>$this->reg_data->unit])->queryOne();
        $mlayanan = self::find()->alias('l')->joinWith([
            'registrasi'=>function($q){
                $q->alias('r')->where(['r.pasien_kode'=>$this->reg_data->pasien_kode]);
            }
        ],false)->andWhere(['l.registrasi_kode'=>$this->reg_data->kode,"TO_CHAR(l.tgl_masuk :: TIMESTAMP,'YYYY-MM-DD HH24:MI')"=>date('Y-m-d H:i',strtotime($this->reg_data->tgl_masuk))])->limit(1)->one();
        $model = $mlayanan!=NULL ? $mlayanan : new Layanan();
        $model->scenario="rj_create";
        $model->registrasi_kode=$this->reg_data->kode;
        $model->jenis_layanan=$jenis_layanan['type'];
        $model->tgl_masuk=$this->reg_data->tgl_masuk;
        $model->unit_kode=$this->setKodeUnit();
        if($model->isNewRecord){
            $model->nomor_urut=$this->generateNoAntrian();
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
            if(!self::isDipanggil($this->reg_data->kode,$this->reg_data->tgl_masuk)){
                if($this->reg_data->unit!=$this->unit_kode){
                    $this->unit_kode=$this->reg_data->unit;
                    $this->nomor_urut=$this->generateNoAntrian();
                }
            }
        }
        return $this->reg_data->unit;
    }
    function generateNoAntrian()
    {
        $find=false;
        $max_now=self::find()->where(['unit_kode'=>$this->unit_kode,"TO_CHAR(tgl_masuk :: DATE,'YYYY-MM-DD')"=>date('Y-m-d')])->notDeleted()->max('nomor_urut');
        $max= !empty($max_now) ? $max_now : 1;
        while(!$find){
            $count=self::find()->where(['nomor_urut'=>$max,'unit_kode'=>$this->unit_kode,"TO_CHAR(tgl_masuk :: DATE,'YYYY-MM-DD')"=>date('Y-m-d')])->notDeleted()->count();
            if($count<1){
                $find=true;
            }else{
                $max++;
            }
        }
        return $max;
    }
    static function searchLayanan($rm)
    {
        $igd=AuthUser::isIgd();
        $data=[];

        //kunjungan
        $kunjungan=Registrasi::countKunjungan($rm);
        $kunjungan = $kunjungan==0 ? ++$kunjungan : $kunjungan;
        
        //query layanan
        $query=self::find()->alias('lay')->joinWith([
            'unit',
            'registrasi'=>function($q) use($rm){
                $q->alias('reg')->joinWith(['pasien','kirimandetail','debiturdetail'])->where(['reg.pasien_kode'=>$rm])
                ->with([
                    'layananhasone'=>function($q){
                        $q->andWhere(['in','jenis_layanan',self::$rj])->orderBy(['tgl_masuk'=>SORT_DESC])->limit(1);
                    }
                ]);
            }
        ])->notDeleted('lay')->orderBy(['lay.tgl_masuk'=>SORT_DESC])->orderBy(['lay.tgl_masuk'=>SORT_DESC])->asArray()->limit(1);
        $query_ri=clone $query;
        $rawatinap=$query_ri->andWhere("lay.jenis_layanan=3 and reg.tgl_keluar is null and lay.tgl_keluar is null and lay.unit_tujuan_kode is null")->one();
        if($rawatinap!=NULL){
            // if($igd){ //jika user igd, show data registrasi rawatinap
            //     $data['registrasi']=[
            //         'kunjungan'=>$kunjungan,
            //         'kode'=>$rawatinap['registrasi']['kode'],
            //         'kiriman'=>$rawatinap['registrasi']['kirimandetail']['kiriman_kode'],
            //         'kiriman_detail_kode'=>$rawatinap['registrasi']['kiriman_detail_kode'],
            //         'debitur'=>$rawatinap['registrasi']['debiturdetail']['debitur_kode'],
            //         'debitur_detail_kode'=>$rawatinap['registrasi']['debitur_detail_kode'],
            //         'no_sep'=>$rawatinap['registrasi']['no_sep'],
            //         'unit'=>$rawatinap['unit_kode'],
            //     ];
            // }else{//jika user loket, show info rawatinap
            $data['status']=false;
            $data['msg']='Pasien : <b>'.$rawatinap['registrasi']['pasien']['nama'].' ( '.$rm.' )</b><br>Terdaftar di Ruang <b>'.( $rawatinap['unit']!=NULL ? $rawatinap['unit']['nama'] : NULL ).'</b><br>Tanggal Masuk : <b>'.date('d-m-Y H:i',strtotime($rawatinap['tgl_masuk'])).'</b><br> Debitur : <b>'.$rawatinap['registrasi']['debiturdetail']['nama'].'</b>';
            // }
        }else{
            $rawatjalan=$query->andWhere(['in','lay.jenis_layanan',self::$rj])->andWhere("TO_CHAR(lay.tgl_masuk :: DATE,'YYYY-MM-DD') = :tgl and lay.tgl_keluar is null",[':tgl'=>date('Y-m-d')])->one();
            if($rawatjalan!=NULL){
                if($igd){ //jika user loket igd
                    if($rawatjalan['unit']['unit_rumpun']!=7){ //jika layanan rawatjalan, show info rawatjalan
                        $data['status']=true;
                        $data['msg']='Pasien No. MR <b>'.$rm.'</b>,<br>Terdaftar ke Unit <b>'.$rawatjalan['unit']['nama'].'</b><br> Tanggal <b>'.date('d-m-Y H:i',strtotime($rawatjalan['tgl_masuk'])).'</b><br><b>Langsung ke MENU RAWATINAP jika pasien masuk RAWATINAP</b>';
                        $data['registrasi']['kunjungan']=$kunjungan;
                        $data['registrasi']['pasien_kode']=$rm;
                        return $data;
                    }
                }else{ //jika user loket rj
                    if($rawatjalan['unit']['unit_rumpun']==7){ //jika terdaftar igd
                        $data['status']=false;
                        $data['msg']='Pasien No. MR : <b>'.$rm.'</b><br>Terdaftar ke Unit : <b>'.$rawatjalan['unit']['nama'].'</b><br>Tanggal : <b>'.date('d-m-Y H:i',strtotime($rawatjalan['tgl_masuk'])).'</b><br>Debitur : <b>'.$rawatjalan['registrasi']['debiturdetail']['nama'].'</b>';
                        return $data;
                    }
                }
                $data['status']=true;
                $data['registrasi']['kunjungan']=$kunjungan;
                $data['registrasi']['pasien_kode']=$rawatjalan['registrasi']['pasien_kode'];
                $data['registrasi']['kode']=$rawatjalan['registrasi']['kode'];
                $data['registrasi']['kiriman']=$rawatjalan['registrasi']['kirimandetail']['kiriman_kode'];
                $data['registrasi']['kiriman_detail_kode']=$rawatjalan['registrasi']['kiriman_detail_kode'];
                $data['registrasi']['debitur']=$rawatjalan['registrasi']['debiturdetail']['debitur_kode'];
                $data['registrasi']['debitur_detail_kode']=$rawatjalan['registrasi']['debitur_detail_kode'];
                $data['registrasi']['no_sep']=$rawatjalan['registrasi']['no_sep'];
                $data['registrasi']['unit']['kode']=$rawatjalan['unit_kode'];
                $data['registrasi']['unit']['status']=self::isDipanggil($rawatjalan['registrasi_kode'],$rawatjalan['unit_kode'],$rawatjalan['tgl_masuk']);
            }else{
                $data['status']=true;
                $data['registrasi']['pasien_kode']=$rm;
                $data['registrasi']['kunjungan']=$kunjungan;
            }
        }
        return $data;     
    }
    static function activeRawatinap($noidentitas,$checkout)
    {
        $query=self::find()->alias('l')->joinWith([
            'unit u',
            'registrasi'=>function($q){
                $q->alias('r')->joinWith(['pasien p'],false);
            },
            'kamar'=>function($q){
                $q->alias('k')->joinWith(['kelas kl','tarif t'],false);
            }
        ],false)->where(['in','l.jenis_layanan',self::$ri])->andWhere(['or',['p.kode'=>Pasien::formatRm($noidentitas)],['p.no_identitas'=>$noidentitas]]);
        if($checkout){
            $query->andWhere('l.tgl_keluar is not null');
        }else{
            $query->andWhere('l.tgl_keluar is null');
        }
        $query->select(['l.id','l.registrasi_kode','l.tgl_masuk','l.unit_kode','l.kamar_id','u.nama as unit','l.kelas_rawat_kode','kl.nama as kelas','k.no_kamar','k.no_kasur','t.biaya as tarif','l.cara_masuk_unit_kode']);
        $result=$query->notDeleted('l')->orderBy(['l.tgl_masuk'=>SORT_DESC])->asArray()->limit(1)->one();
        if($result!=NULL){
            $result['tgl_masuk']=date('d-m-Y H:i:s',strtotime($result['tgl_masuk']));
            $result['dokter']=PjpRi::getListDpjp($result['registrasi_kode']);
            return $result;
        }
        return NULL;
    }
    static function latestRawatjalan($noidentitas)
    {
        $result= self::find()->alias('l')->joinWith([
            'unit u',
            'registrasi'=>function($q){
                $q->alias('r')->joinWith(['pasien p','debiturdetail dd','kirimandetail kd'],false);
            }
        ],false)->where(['or',['p.kode'=>Pasien::formatRm($noidentitas)],['p.no_identitas'=>$noidentitas]])
        ->andWhere(['in','jenis_layanan',self::$rj])->andWhere(["between","TO_CHAR(l.tgl_masuk :: DATE,'YYYY-MM-DD')",date('Y-m-d',strtotime("-1 days")),date('Y-m-d')])->notDeleted('l')
        ->select(['l.registrasi_kode','u.nama as unit','l.tgl_masuk','dd.nama as debitur'])
        ->orderBy(['l.tgl_masuk'=>SORT_DESC])->asArray()->limit(1)->one();
        if($result!=NULL){
            $result['tgl_masuk']=date('d-m-Y H:i:s',strtotime($result['tgl_masuk']));
            $result['dokter']=PjpRi::getListDpjp($result['registrasi_kode']);
        }
        return $result;
    }
    static function getNowLayanan($rm,$jenis)
    {
        //jenis 1 rawatinap,2 rawatjalan
        $model= self::find()->alias('l')->joinWith([
            'unit u',
            'registrasi'=>function($q) use($rm){
                $q->alias('r')->joinWith(['pasien p'],false)->where(['r.pasien_kode'=>Pasien::formatRm($rm)]);
            }
        ],false)->notDeleted('l')->andWhere(['l.jenis_layanan'=>$jenis,"TO_CHAR(l.tgl_masuk :: DATE,'YYYY-MM-DD')"=>date('Y-m-d')])->select('p.nama,p.tempat_lahir,p.tgl_lahir,r.kode,r.pasien_kode,l.tgl_masuk,u.nama as unit,l.panggil_perawat,l.unit_asal_kode')->orderBy(['tgl_masuk'=>SORT_DESC])->asArray()->limit(1)->one();
        if($model!=NULL){
            $model['tgl_lahir']=date('d-m-Y',strtotime($model['tgl_lahir']));
            $model['tgl_masuk']=date('d-m-Y H:i:s',strtotime($model['tgl_masuk']));
            return $model;
        }
        return false;
    }
    function saveRawatinap()
    {
        $transaction = self::getDb()->beginTransaction();
        try {
            $this->jenis_layanan=3;
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
}