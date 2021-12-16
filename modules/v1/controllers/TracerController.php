<?php

namespace app\modules\v1\controllers;

use app\helpers\BehaviorsFromParamsHelper;
use yii\rest\ActiveController;
use app\models\Status;
use app\models\Registrasi;
use yii\data\Pagination;
use Yii;
/*
 *
 * @author Heru Arief Wijaya
 * 2020 @  belajararief.com
 */

class TracerController extends ActiveController
{
    public $modelClass = 'app\models\Registrasi';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors = BehaviorsFromParamsHelper::behaviors($behaviors);
        return $behaviors;
    }
    public function actionShow()
    {

        return [
            'status' => Status::STATUS_OK,
            'message' => 'Hello :)',
            'data' => 'hy'
        ];
    }
    public function actionData()
    {
        $req = Yii::$app->request;
        $tanggal = $req->post('tanggal');
        $cari = $req->post('cari');

        $model = Registrasi::find()->joinWith(['pasien', 'kirimandetail', 'debiturdetail'], false)->with([
            'layanan' => function ($q) {
                $q->joinWith(['unit'])->select(['pl_reg_kode', 'pl_unit_kode', 'pl_jenis_layanan', 'pl_tgl_masuk', 'pl_tgl_keluar', 'unt_nama as nama_unit'])->orderBy(['pl_tgl_masuk' => SORT_ASC]);
            }
        ])->select(['reg_kode', 'reg_kode as no_registrasi', 'ps_kode as no_rm', 'ps_nama', 'reg_tgl_masuk', 'reg_tgl_keluar', 'pmkd_nama as kirimandetail', 'pmdd_nama as debiturdetail', 'reg_is_print'])->andwhere(["DATE_FORMAT(reg_created_at,'%Y-%m-%d')" => $tanggal])->andWhere(
            [
                'or', ['like', 'ps_nama', '%' . $cari . '%', false],
                ['like', 'ps_kode', '%' . $cari . '%', false],
                ['like', 'reg_kode', '%' . $cari . '%', false],
            ]
        )->notDeleted(Registrasi::$prefix)->asArray()->all();
        $count = Registrasi::find()->joinWith(['pasien', 'kirimandetail', 'debiturdetail'], false)->with([
            'layanan' => function ($q) {
                $q->joinWith(['unit'])->select(['pl_reg_kode', 'pl_unit_kode', 'pl_jenis_layanan', 'pl_tgl_masuk', 'pl_tgl_keluar', 'unt_nama as nama_unit'])->orderBy(['pl_tgl_masuk' => SORT_ASC]);
            }
        ])->select(['reg_kode', 'reg_kode as no_registrasi', 'ps_kode as no_rm', 'ps_nama', 'reg_tgl_masuk', 'reg_tgl_keluar', 'pmkd_nama as kirimandetail', 'pmdd_nama as debiturdetail', 'reg_is_print'])->andwhere(["DATE_FORMAT(reg_created_at,'%Y-%m-%d')" => $tanggal])->andWhere(
            [
                'or', ['like', 'ps_nama', '%' . $cari . '%', false],
                ['like', 'ps_kode', '%' . $cari . '%', false],
                ['like', 'reg_kode', '%' . $cari . '%', false],
            ]
        )->notDeleted(Registrasi::$prefix)->asArray()->count();

        $data = array();

        foreach ($model as $mod) {
            $data[] = [
                'tanggal' => $mod['reg_tgl_masuk'] ?? '-',
                'rm' => $mod['no_rm'] ?? '-',
                'pasien' => $mod['ps_nama'] ?? '-',
                'poli' => $mod['layanan'][0]['nama_unit'] ?? '-',
                'debitur' => $mod['debiturdetail'] ?? '-',

                'noreg' => $mod['no_registrasi'] ?? '-',
                'status' => $mod['reg_is_print'] == '0' ? '<span class="badge badge-danger">Belum Cetak</span>' : '<span class="badge badge-success">Sudah Cetak</span>',

            ];
        }

        return [
            'status' => Status::STATUS_OK,
            'message' => 'Data successfull',
            'data' => $data,

            // "draw"=>intval($_POST['draw']),
            "recordsTotal" => $count,
            "recordsFiltered" => $count,



        ];
    }

    public function actionPrintManual()
    {
        $req = Yii::$app->request;
        $noreg = $req->post('noreg');
        $reg = Registrasi::find()->joinWith(['pasien', 'kirimandetail', 'debiturdetail'], false)->with([
            'layanan' => function ($q) {
                $q->joinWith(['unit'])->select(['pl_reg_kode', 'pl_unit_kode', 'pl_jenis_layanan', 'pl_tgl_masuk', 'pl_tgl_keluar', 'unt_nama as nama_unit'])->orderBy(['pl_tgl_masuk' => SORT_ASC]);
            }
        ])->select(['reg_kode', 'reg_kode as no_registrasi', 'ps_kode as no_rm', 'ps_nama', 'reg_tgl_masuk', 'reg_tgl_keluar', 'pmkd_nama as kirimandetail', 'pmdd_nama as debiturdetail', 'reg_is_print'])->andwhere(['reg_kode' => $noreg])->notDeleted(Registrasi::$prefix)->asArray()->one();
        $registrasi = Registrasi::find()->where(['reg_kode' => $reg['no_registrasi']])->one();
        if ($registrasi != null) {
            $registrasi->reg_is_print = 1;
            $registrasi->save();
        }


        $data = [
            'tanggal' => $reg['reg_tgl_masuk'] ?? '-',
            'pasien' => $reg['ps_nama'] ?? '-',
            'poli' => $reg['layanan'][0]['nama_unit'] ?? '-',
            'debitur' => $reg['debiturdetail'] ?? '-',
            'rm' => $reg['no_rm'] ?? '-',
            'noreg' => $reg['no_registrasi'] ?? '-',
        ];

        return [
            'status' => Status::STATUS_OK,
            'message' => 'Data successfull',
            'data' => $data,

        ];
    }
    public function actionPrintOtomatis()
    {
        $reg = Registrasi::find()->joinWith(['pasien', 'kirimandetail', 'debiturdetail'], false)->with([
            'layanan' => function ($q) {
                $q->joinWith(['unit'])->select(['pl_reg_kode', 'pl_unit_kode', 'pl_jenis_layanan', 'pl_tgl_masuk', 'pl_tgl_keluar', 'unt_nama as nama_unit',])->orderBy(['pl_tgl_masuk' => SORT_ASC]);
            }
        ])->select(['reg_kode', 'reg_kode as no_registrasi', 'ps_kode as no_rm', 'ps_nama', 'reg_tgl_masuk', 'reg_tgl_keluar', 'pmkd_nama as kirimandetail', 'pmdd_nama as debiturdetail', 'reg_is_print'])->andwhere(["DATE_FORMAT(reg_created_at,'%Y-%m-%d')" => date('Y-m-d')])->andwhere(['reg_is_print' => '0'])->notDeleted(Registrasi::$prefix)->orderBy('reg_tgl_masuk', 'ASC')->asArray()->one();
        // return $reg;
        $data = null;
        if ($reg != null) {
            $registrasi = Registrasi::find()->where(['reg_kode' => $reg['no_registrasi']])->one();
            $registrasi->reg_is_print = 1;
            $registrasi->save();

            // foreach($registrasi as $reg){
            $data = [
                'tanggal' => $reg['reg_tgl_masuk'] ?? '-',
                'pasien' => $reg['ps_nama'] ?? '-',
                'poli' => $reg['layanan'][0]['nama_unit'] ?? '-',
                'debitur' => $reg['debiturdetail'] ?? '-',
                'rm' => $reg['no_rm'] ?? '-',
                'noreg' => $reg['no_registrasi'] ?? '-',
            ];
            // }
        }
        return [
            'status' => Status::STATUS_OK,
            'message' => 'Data successfull',
            'data' => $data,

        ];
    }

    public function actionTracerDatatable()
    {
        $req = Yii::$app->request;

        $req = $req->post('datatables');


        $model = Registrasi::find()->joinWith(['pasien', 'kirimandetail', 'debiturdetail', 'distribusi'])->with([
            'layanan' => function ($q) {
                $q->joinWith(['unit']);
            }
        ]);

        if ($req['tanggal']) {
            $model->andwhere(["DATE_FORMAT(reg_created_at,'%Y-%m-%d')" => $req['tanggal']]);
        } else {
            $model->andwhere(["DATE_FORMAT(reg_created_at,'%Y-%m-%d')" => date('Y-m-d')]);
        }
        if ($req['cari']) {
            $model->andWhere(
                [
                    'or', ['like', 'ps_nama', '%' . $req['cari'] . '%', false],
                    ['like', 'ps_kode', '%' . $req['cari'] . '%', false],
                    ['like', 'reg_kode', '%' . $req['cari'] . '%', false],
                ]
            );
        }


        $data_filter_length = $model->count();
        $data_length = $model->count();
        $model->limit($req['length'])->offset($req['start']);
        $dir = $req['order'][0]['dir'] === 'asc' ? SORT_ASC : SORT_DESC;
        $model->orderBy([$req['columns'][$req['order'][0]['column']]['name'] => $dir]);
        $data = $model->asArray()->all();

        $result = [
            "draw" => isset($req['draw']) ? intval($req['draw']) : 0,
            "recordsTotal" => intval($data_length),
            "recordsFiltered" => intval($data_filter_length),
            "data" => $data
        ];
        return $this->asJson($result);
    }
}
