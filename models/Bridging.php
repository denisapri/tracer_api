<?php
namespace app\models;
use app\widgets\AuthUser;
class Bridging extends BridgingBase
{
    //------------------ BEGIN Rujukan--------------------------
    function queryMultiRujukan($nomor,$tipe=1)
    {
        $param=['Rujukan'];
        if($tipe==2){//Rumah Sakit
            $param[]="RS";
        }
        $param[]='List';
        $param[]='Peserta';
        $param[]=$nomor;
        $this->setUpUrl($param);
        return $this;
    }

    //TIPE = 1: No Rujukan, 2:No Kartu
    function queryGetRujukan($parameter,$tipe = 1, $faskes = 1)
    {
        $param = ['Rujukan'];

        if($faskes == 2){
            $param[] = 'Rs';
        }

        if($tipe == 2){
            $param[] = 'Peserta';
        }

        $param[] = $parameter;
        $this->setUpUrl($param);
        return $this;
    }
    public function insertRujukan($model)
    {
        $data = [
            'request'=>[
                't_rujukan'=>[
                    "noSep"=> (string) $model->ruj_no_sep,
                    "tglRujukan"=> date('Y-m-d',strtotime($model->ruj_tgl_rujukan)),
                    "ppkDirujuk"=> (string) $model->ruj_ppk_dirujuk_kode,
                    "jnsPelayanan"=> (string) $model->ruj_jenis_pelayanan,
                    "catatan"=> (string) $model->ruj_catatan,
                    "diagRujukan"=> (string) $model->ruj_diagnosa_kode,
                    "tipeRujukan"=> (string) $model->ruj_tipe_rujukan,
                    "poliRujukan"=> (string) $model->ruj_poli_kode,
                    "user"=> (string) AuthUser::user()->id
                ]
            ]
        ];
        $this->setUpUrl(['Rujukan','insert'],json_encode($data),'POST');
        return $this;
    }
    public function updateRujukan($model)
    {
        $data = [
            'request'=>[
                't_rujukan'=>[
                    "noRujukan"=> (string) $model->ruj_no_rujukan,
                    "ppkDirujuk"=> (string) $model->ruj_ppk_dirujuk_kode,
                    "tipe"=>(string) $model->ruj_tipe_rujukan,
                    "jnsPelayanan"=> (string) $model->ruj_jenis_pelayanan,
                    "catatan"=> (string) $model->ruj_catatan,
                    "diagRujukan"=> (string) $model->ruj_diagnosa_kode,
                    "tipeRujukan"=> (string) $model->ruj_tipe_rujukan,
                    "poliRujukan"=> (string) $model->ruj_poli_kode,
                    "user"=> (string) AuthUser::user()->id,
                ]
            ]
        ];

        $this->setUpUrl(['Rujukan','update'],json_encode($data),'PUT');
        return $this;
    }
    public function deleteRujukan($data)
    {
        $this->setUpUrl(['Rujukan','delete'],json_encode($data),'DELETE');
        return $this;
    }
    //------------------ END Rujukan----------------------------

    //------------------ BEGIN Monitoring----------------------------
    function queryHistoryPelayananPeserta($nokartu,$tglawal=NULL,$tglakhir=NULL)
    {
        $this->setUpUrl(['monitoring','HistoriPelayanan','NoKartu',$nokartu,'tglAwal',$tglawal,'tglAkhir',$tglakhir]);
        return $this;
    }
    //------------------ END Monitoring----------------------------



    function queryKunjungan($nokartu)
    {
        $this->setUpUrl(['Monitoring','Kunjungan','Tanggal',$nokartu]);
        return $this;
    }
    function queryGetSuplesiJasaRaharja($nobpjs)
    {
        $this->setUpUrl(['sep','JasaRaharja','Suplesi',$nobpjs,'tglPelayanan',date('Y-m-d')]);
        return $this;
    }
    function queryGetDokterDpjp($kode,$jenis,$tgl)
    {
        $this->setUpUrl(['referensi','dokter','pelayanan',$jenis,'tglPelayanan',$tgl,'Spesialis',$kode]);
        return $this;
    }
    function queryGetProvinsi()
    {
        $this->setUpUrl(['referensi','propinsi']);
        return $this;
    }
    function queryGetKabupaten($prov)
    {
        $this->setUpUrl(['referensi','kabupaten','propinsi',$prov]);
        return $this;
    }
    function queryGetKecamatan($kab)
    {
        $this->setUpUrl(['referensi','kecamatan','kabupaten',$kab]);
        return $this;
    }
    public function queryGetListPoli($poli)
    {
        $this->setUpUrl(['referensi','poli',$poli]);
        return $this;
    }

    public function queryGetListDiagnosa($diagnosa)
    {
        $this->setUpUrl(['referensi','diagnosa',$diagnosa]);
        return $this;
    }

    public function queryGetListFaskes($parameter, $tipe = 1)
    {
        $this->setUpUrl(['referensi','faskes',$parameter, $tipe]);
        return $this;
    }

    public function queryGetPeserta($nomor, $tipe = 1) //1: No.Kartu, 2:NIK
    {
        if($tipe == 1){
            $this->setUpUrl(['Peserta', 'nokartu', $nomor, 'tglSEP', date('Y-m-d')]);
        }else if($tipe == 2){
            $this->setUpUrl(['Peserta','nik',$nomor, 'tglSEP', date('Y-m-d')]);
        }
        return $this;
    }

    

    public function queryGetRiwayatSEP($noKartu)
    {
        $this->setUpUrl(['SEP','Riwayat',$noKartu]);
        return $this;
    }

    public function querySearchSEP($noSEP)
    {
        $this->setUpUrl(['SEP',$noSEP]);
        return $this;
    }

    public function queryHapusSEP($data)
    {
        $this->setUpUrl(['SEP','Delete'], json_encode($data),'DELETE');
        return $this;
    }

    public function queryInsertSEP($data)
    {
        if(empty($data['request']['t_sep']["rujukan"]["noRujukan"])){
            $data['request']['t_sep']["rujukan"]["noRujukan"] = "0";
        }
        $this->setUpUrl(['SEP','1.1','insert'],json_encode($data),'POST');
        return $this;
    }

    public function queryUpdateSEP($data)
    {
        $this->setUpUrl(['SEP','1.1','Update'],json_encode($data),'PUT');
        return $this;
    }

    public function queryCheckOutSEP($data)
    {
        $this->setUpUrl(['Sep','updtglplg'],json_encode($data),'PUT');
        return $this;
    }

    public function pengajuanSEP($data)
    {
        $this->setUpUrl(['Sep','pengajuanSEP'], json_encode($data),'POST');
        return $this;
    }

    public function approvalSEP($data)
    {
        $this->setUpUrl(['Sep','aprovalSEP'], json_encode($data),'POST');
        return $this;
    }

    public function monitoringKunjungan($tanggal, $tipe)
    {
        $this->setUpUrl(['Monitoring','Kunjungan','Tanggal',date('Y-m-d',strtotime($tanggal)),'JnsPelayanan',$tipe],null,'GET');
        return $this;
    }

    public function monitoringKlaim($tanggal, $tipe, $status)
    {
        $this->setUpUrl(['Monitoring','Klaim','Tanggal',date('Y-m-d',strtotime($tanggal)),'JnsPelayanan',$tipe,'Status'=>$status],null,'GET');
        return $this;
    }

    

    

    

    public function getProsedur($data)
    {
        $this->setUpUrl(['referensi','procedure',$data],null,'GET');
        return $this;
    }

}