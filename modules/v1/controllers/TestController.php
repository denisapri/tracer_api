<?php

namespace app\modules\v1\controllers;

use app\helpers\BehaviorsFromParamsHelper;
use yii\rest\ActiveController;
use app\models\Status;
use app\models\Registrasi;
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

    public function actionList()
    {
        $registrasi = Registrasi::find()->where(["DATE_FORMAT(reg_tgl_masuk,'%Y-%m-%d')"=>'2021-04-18'])->all();
      
        $data=[
            ];
        foreach($registrasi as $reg){
            $data[]=[
                'reg_kode'=>$reg->reg_kode,
                'reg_created_at'=>$reg->reg_created_at,
                'ps_nama'=>$reg->pasien['ps_nama']??'',
                'ps_kode'=>$reg->pasien['ps_kode']??'',
                'pl_id'=>$reg->getLayanan()->one()['pl_id']??'',
                'debitur'=>$reg->debiturdetail->pmdd_nama,
                'unit'=>$reg->getLayanan()->one()->unit->unt_nama
                ];
        }
        
        return [
            'status' => Status::STATUS_OK,
            'message' => 'Hello :)',
            'data' => 
				$data
			
        ];
    //     return [
    //         'status' => Status::STATUS_OK,
    //         'message' => 'Hello :)',
    //         'data' => 
				// $post
			
    //     ];
    }

}