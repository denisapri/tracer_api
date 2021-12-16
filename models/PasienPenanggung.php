<?php
namespace app\models;
use Yii;
class PasienPenanggung extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'pendaftaran_pasien_penanggung';
    }
    public function rules()
    {
        return [
            [['pen_pasien_kode', 'pen_pmd_kode', 'pen_pmdd_kode', 'pen_no_kartu'], 'required'],
            [['pen_created_at'], 'safe'],
            [['pen_created_by'], 'integer'],
            [['pen_pasien_kode', 'pen_pmd_kode', 'pen_pmdd_kode'], 'string', 'max' => 10],
            [['pen_no_kartu'], 'string', 'max' => 50],
        ];
    }
    public function attributeLabels()
    {
        return [
            'pen_id' => 'Pen ID',
            'pen_pasien_kode' => 'Pen Pasien Kode',
            'pen_pmd_kode' => 'Pen Pmd Kode',
            'pen_pmdd_kode' => 'Pen Pmdd Kode',
            'pen_no_kartu' => 'Pen No Kartu',
            'pen_created_at' => 'Pen Created At',
            'pen_created_by' => 'Pen Created By',
        ];
    }
    function getDebitur()
    {
        return $this->hasOne(Debitur::className(),['pmd_kode'=>'pen_pmd_kode']);
    }
    function getDebiturdetail()
    {
        return $this->hasOne(DebiturDetail::className(),['pmdd_kode'=>'pen_pmdd_kode']);
    }
    function savePasienPenanggung($obj)
    {
        Yii::$app->db->createCommand()->delete(self::tableName(),['pen_pasien_kode'=>$obj->ps_kode])->execute();
        if($obj->pen_nama!=NULL){
            $tmp=[];
            foreach($obj->pen_nama as $key => $k){
                $i=explode('_',$k);
                $tmp[]=[$obj->ps_kode,$i[0],$i[1],$obj->pen_nomor[$key],date('Y-m-d H:i:s')];
            }
            // dd($tmp);
            if(count($tmp)>0){
                $save=Yii::$app->db->createCommand()->batchInsert(self::tableName(),['pen_pasien_kode','pen_pmd_kode','pen_pmdd_kode','pen_no_kartu','pen_created_at'],$tmp)->execute();
                if($save){
                    return true;
                }
                return false;
            }
        }
        return true;
    }
    static function getBpjsKesehatan($rm)
    {
        $bpjs=Yii::$app->params['bpjs']['app']['id'];
        $data= self::find()->select('pen_no_kartu')->where(['pen_pasien_kode'=>$rm,'pen_pmdd_kode'=>$bpjs])->asArray()->limit(1)->one();
        if($data!=NULL){
            return $data['pen_no_kartu'];
        }
        return NULL;
    }
}
