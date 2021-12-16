<?php
namespace app\models;
use Yii;
use app\widgets\AuthUser;
class PjpRi extends \yii\db\ActiveRecord
{
    public $layanan_data;
    static $prefix="pjpri";
    static $status_pjp=[1=>'Dokter Utama',2=>'Dokter Pendukung',3=>'Perawat',4=>'Bidang'];
    public static function tableName()
    {
        return 'medis_pjp_ri';
    }
    public function rules()
    {
        return [
            [['pjpri_reg_kode', 'pjpri_pgw_id', 'pjpri_status', 'pjpri_ket'], 'required'],
            [['pjpri_pgw_id', 'pjpri_status', 'pjpri_created_by', 'pjpri_updated_by', 'pjpri_deleted_by'], 'integer'],
            [['pjpri_tgl_mulai', 'pjpri_tgl_akhir', 'pjpri_created_at', 'pjpri_updated_at', 'pjpri_deleted_at'], 'safe'],
            [['pjpri_ket'], 'string'],
            [['pjpri_reg_kode'], 'string', 'max' => 10],
        ];
    }
    public function attributeLabels()
    {
        return [
            'pjpri_id' => 'Pjpri ID',
            'pjpri_reg_kode' => 'Pjpri Reg Kode',
            'pjpri_pgw_id' => 'Pjpri Pgw ID',
            'pjpri_status' => 'Pjpri Status',
            'pjpri_tgl_mulai' => 'Pjpri Tgl Mulai',
            'pjpri_tgl_akhir' => 'Pjpri Tgl Akhir',
            'pjpri_ket' => 'Pjpri Ket',
            'pjpri_created_at' => 'Pjpri Created At',
            'pjpri_created_by' => 'Pjpri Created By',
            'pjpri_updated_at' => 'Pjpri Updated At',
            'pjpri_updated_by' => 'Pjpri Updated By',
            'pjpri_deleted_at' => 'Pjpri Deleted At',
            'pjpri_deleted_by' => 'Pjpri Deleted By',
        ];
    }
    static function find()
    {
        return new BaseQuery(get_called_class());
    }
    function getPegawai()
    {
        return $this->hasOne(Pegawai::className(),['pgw_id'=>'pjpri_pgw_id']);
    }
    static function getListDpjp($no_reg)
    {
        $result= self::find()->joinWith([
            'pegawai'=>function($q){
                $q->joinWith(['penempatan'],false);
            }
        ],false)->select('pgw_id,pgw_gelar_depan,pgw_nama,pgw_gelar_belakang,pjpri_status')->where(['pjpri_reg_kode'=>$no_reg])->notDeleted(self::$prefix)->asArray()->all();
        $result=array_map(function($q){
            $status=self::$status_pjp[$q['pjpri_status']];
            return ['id'=>$q['pgw_id'],'name'=>trim($q['pgw_gelar_depan']).' '.$q['pgw_nama'].' '.trim($q['pgw_gelar_belakang']).''.($status!=NULL ? ' ( '.$status.' )' : NULL),'status'=>$q['pjpri_status']];
        },$result);
        return $result;
    }
    function saveDpjpRi()
    {
        $new=$this->layanan_data->dokter;
        if($new!=NULL){
            $old=array_column(Yii::$app->db->createCommand("SELECT pjpri_reg_kode,pjpri_pgw_id FROM ".self::tableName()." WHERE pjpri_reg_kode = :noreg AND pjpri_deleted_at IS NULL")->bindValues([':noreg'=>$this->layanan_data->pl_reg_kode])->queryAll(),'pjpri_pgw_id');
            $insert=array_diff($new,$old);
            $delete=array_diff($old,$new);
            //insert new
            if(count($insert)>0){
                $tmp=[];
                foreach($insert as $k => $v){
                    $tmp[]=[
                        $this->layanan_data->pl_reg_kode,
                        $v,
                        $this->layanan_data->status[$k],
                        date('Y-m-d H:i:s'),
                        date('Y-m-d H:i:s'),
                        AuthUser::user()->id
                    ];
                }
                if(count($tmp)>0){
                    Yii::$app->db->createCommand()->batchInsert(self::tableName(), ['pjpri_reg_kode', 'pjpri_pgw_id','pjpri_status','pjpri_tgl_mulai','pjpri_created_at','pjpri_created_by'], $tmp)->execute();
                }
            }
            //delete old
            if(count($delete)>0){
                foreach($delete as $k => $v){
                    Yii::$app->db->createCommand()->update(self::tableName(),['pjpri_deleted_at'=>date('Y-m-d H:i:s'),'pjpri_deleted_by'=>AuthUser::user()->id],['pjpri_reg_kode'=>$this->layanan_data->pl_reg_kode,'pjpri_pgw_id'=>$v])->execute();
                }
            }
        }
        return true;
    }
    function deleteDpjpRi()
    {
        Yii::$app->db->createCommand()->update(self::tableName(),['pjpri_deleted_at'=>date('Y-m-d H:i:s'),'pjpri_deleted_by'=>AuthUser::user()->id],['pjpri_reg_kode'=>$this->layanan_data->pl_reg_kode])->execute();
        return true;
    }
}