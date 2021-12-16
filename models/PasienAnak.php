<?php
namespace app\models;
use Yii;
class PasienAnak extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'pendaftaran_pasien_anak';
    }
    public function rules()
    {
        return [
            [['ppa_pasien_kode', 'ppa_tgl_lahir', 'ppa_status', 'ppa_no_rekam_medis', 'ppa_created_by'], 'required'],
            [['ppa_tgl_lahir', 'ppa_created_at'], 'safe'],
            [['ppa_status', 'ppa_created_by'], 'integer'],
            [['ppa_pasien_kode', 'ppa_no_rekam_medis'], 'string', 'max' => 10],
        ];
    }
    public function attributeLabels()
    {
        return [
            'ppa_id' => 'Ppa ID',
            'ppa_pasien_kode' => 'Ppa Pasien Kode',
            'ppa_tgl_lahir' => 'Ppa Tgl Lahir',
            'ppa_status' => 'Ppa Status',
            'ppa_no_rekam_medis' => 'Ppa No Rekam Medis',
            'ppa_created_at' => 'Ppa Created At',
            'ppa_created_by' => 'Ppa Created By',
        ];
    }
    function saveAnakPasien($obj)
    {
        Yii::$app->db->createCommand()->delete(self::tableName(),['ppa_pasien_kode'=>$obj->ps_kode])->execute();
        if($obj->anak_nama!=NULL){
            $tmp=[];
            foreach($obj->anak_nama as $key => $k){
                $tmp[]=[$obj->ps_kode,$obj->anak_nama[$key],date('Y-m-d',strtotime($obj->anak_tgl[$key])),$obj->anak_status[$key],isset($obj->anak_mr) ? $obj->anak_mr[$key] : ''];
            }
            if(count($tmp)>0){
                $save=Yii::$app->db->createCommand()->batchInsert(self::tableName(),['ppa_pasien_kode','ppa_nama','ppa_tgl_lahir','ppa_status','ppa_no_rekam_medis'],$tmp)->execute();
                if($save){
                    return true;
                }
                return false;
            }
        }
        return true;
    }
}