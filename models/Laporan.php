<?php
namespace app\models;
use Yii;
use app\widgets\AuthUser;
class Laporan 
{
    static function laporanPerUnit($req)
    {
        $tgl_awal=$req->post('tgl_awal');
        $tgl_akhir=$req->post('tgl_akhir');
        $unit=$req->post('unit');
        $debitur=$req->post('debitur');
        $query = Registrasi::find()->where(['in','pl_jenis_layanan',[Registrasi::$igd,Registrasi::$rj]])->andWhere(['between',"DATE_FORMAT(reg_tgl_masuk,'%Y-%m-%d')",date('Y-m-d',strtotime($tgl_awal)),date('Y-m-d',strtotime($tgl_akhir))])
        ->notDeleted(Registrasi::$prefix)->joinWith([
            'debiturdetail',
            'pasien',
            'layanan'=>function($q){
                $q->joinWith(['unit'],false);
            }
        ],false);
        if(isset($debitur) && count($debitur)>0){
            $query->andWhere(['in','reg_pmdd_kode',$debitur]);
        }
        if(isset($unit) && count($unit)>0){
            $query->andWhere(['in','pl_unit_kode',$unit]);
        }
        return $query->select(['ps_kode','reg_kode','ps_nama','unt_nama','reg_tgl_masuk','pmdd_nama'])->orderBy(['reg_tgl_masuk'=>SORT_ASC])->asArray()->all();
    }
    static function totalPerDebitur($req)
    {
        $tgl_awal=$req->post('tgl_awal');
        $tgl_akhir=$req->post('tgl_akhir');
        $unit=$req->post('unit');
        $debitur=$req->post('debitur');

        $query_unit=Unit::find();
        if(isset($unit) && count($unit)>0){
            $query_unit->andWhere(['in','unt_id',$query_unit]);
        }
        $unit_data=$query_unit->select(['unt_id','unt_nama'])->asArray()->all();

        $query_debitur=DebiturDetail::find();
        if(isset($debitur) && count($debitur)>0){
            $query_debitur->andWhere(['in','pmdd_kode',$debitur]);
        }
        $debitur_data=$query_debitur->select(['pmdd_kode','pmdd_nama'])->asArray()->all();
        
        $result=[];
        if(count($unit_data)>0){
            foreach($unit_data as $ud){
                if(count($debitur_data)>0){
                    foreach($debitur_data as $dd){
                        $total = Layanan::find()
                        ->joinWith(['registrasi'],false)
                        ->where(['in','pl_jenis_layanan',[Layanan::$igd,Layanan::$rj]])
                        ->andWhere(['pl_unit_kode'=>$ud['unt_id'],'reg_pmdd_kode'=>$dd['pmdd_kode']])
                        ->andWhere(['between',"DATE_FORMAT(reg_tgl_masuk,'%Y-%m-%d')",date('Y-m-d',strtotime($tgl_awal)),date('Y-m-d',strtotime($tgl_akhir))])
                        ->notDeleted(Layanan::$prefix)->count();
                        $result[$ud['unt_nama']][$dd['pmdd_nama']]=$total;
                    }
                }
            }
        }
        return $result;
    }
    static function kunjunganRawatinap($req)
    {
        $tgl_awal=$req->post('tgl_awal');
        $tgl_akhir=$req->post('tgl_akhir');
        $unit=$req->post('unit');
        $debitur=$req->post('debitur');

        $query=Layanan::find()->where(['in','pl_jenis_layanan',[Registrasi::$ri]])->andWhere('pl_tgl_keluar is null')
        ->joinWith([
            'kelas',
            'unit',
            'registrasi'=>function($q){
                $q->joinWith(['pasien','debiturdetail'],false);
            },
            'kamar'],false)
        ->andWhere(['between',"DATE_FORMAT(pl_tgl_masuk,'%Y-%m-%d')",date('Y-m-d',strtotime($tgl_awal)),date('Y-m-d',strtotime($tgl_akhir))])
        ->notDeleted(Registrasi::$prefix);
        if(isset($debitur) && count($debitur)>0){
            $query->andWhere(['in','reg_pmdd_kode',$debitur]);
        }
        if(isset($unit) && count($unit)>0){
            $query->andWhere(['in','pl_unit_kode',$unit]);
        }
        return $query->select(['reg_pasien_kode','reg_kode','ps_nama','unt_nama','kmr_no_kamar','kmr_no_kasur','kr_nama','pl_tgl_masuk','pmdd_nama'])->asArray()->all();
    }
    static function pulangRawatinap($req)
    {
        $tgl_awal=$req->post('tgl_awal');
        $tgl_akhir=$req->post('tgl_akhir');
        $unit=$req->post('unit');
        $debitur=$req->post('debitur');

        $query=Layanan::find()->where(['in','pl_jenis_layanan',[Registrasi::$ri]])->andWhere('pl_tgl_keluar is not null')
        ->joinWith([
            'kelas',
            'unit',
            'registrasi'=>function($q){
                $q->joinWith(['pasien','debiturdetail'],false);
            },
            'kamar'],false)
        ->andWhere(['between',"DATE_FORMAT(pl_tgl_keluar,'%Y-%m-%d')",date('Y-m-d',strtotime($tgl_awal)),date('Y-m-d',strtotime($tgl_akhir))])
        ->notDeleted(Registrasi::$prefix);
        if(isset($debitur) && count($debitur)>0){
            $query->andWhere(['in','reg_pmdd_kode',$debitur]);
        }
        if(isset($unit) && count($unit)>0){
            $query->andWhere(['in','pl_unit_kode',$unit]);
        }
        return $query->select(['reg_pasien_kode','reg_kode','ps_nama','unt_nama','kmr_no_kamar','kmr_no_kasur','kr_nama','pl_tgl_masuk','pl_tgl_keluar','pmdd_nama'])->asArray()->all();
    }
}