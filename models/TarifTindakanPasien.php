<?php
namespace app\models;
use Yii;
use app\widgets\AuthUser;
class TarifTindakanPasien extends \yii\db\ActiveRecord
{
    public $error_msg,$reg_data,$layanan_data,$tarif_tindakan_data;
    public static function tableName()
    {
        return 'medis.tarif_tindakan_pasien';
    }
    public function rules()
    {
        return [
            [['layanan_id', 'tarif_tindakan_id', 'tanggal', 'harga', 'subtotal', 'created_by'], 'required','on'=>'create','message'=>'{attribute} harus diisi'],
            [['layanan_id', 'pelaksana_id', 'tarif_tindakan_id', 'cyto', 'jumlah', 'harga', 'subtotal', 'pembayaran_id', 'is_lis', 'is_pac', 'created_by', 'updated_by', 'is_deleted'], 'default', 'value' => null],
            [['layanan_id', 'pelaksana_id', 'tarif_tindakan_id', 'cyto', 'jumlah', 'harga', 'subtotal', 'pembayaran_id', 'is_lis', 'is_pac', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['tanggal', 'created_at', 'updated_at'], 'safe'],
            [['keterangan', 'log_data'], 'string'],
            [['no_permintaan_alat'], 'string', 'max' => 20],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'layanan_id' => 'Layanan ID',
            'pelaksana_id' => 'Pelaksana ID',
            'tarif_tindakan_id' => 'Tarif Tindakan ID',
            'tanggal' => 'Tanggal',
            'cyto' => 'Cyto',
            'jumlah' => 'Jumlah',
            'harga' => 'Harga',
            'subtotal' => 'Subtotal',
            'keterangan' => 'Keterangan',
            'no_permintaan_alat' => 'No Permintaan Alat',
            'pembayaran_id' => 'Pembayaran ID',
            'is_lis' => 'Is Lis',
            'is_pac' => 'Is Pac',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'log_data' => 'Log Data',
            'is_deleted' => 'Is Deleted',
        ];
    }
    function attr()
    {
        $data=[];
        foreach($this->attributeLabels() as $key => $val){
            $data[$val]=$this->{$key};
        }
        return $data;
    }
    function saveTarif()
    {
        $tarif_registrasi=KelompokUnitLayanan::getTarifKonsultasi($this->reg_data->unit);
        if($tarif_registrasi!=NULL){
            $count=Yii::$app->db->createCommand("SELECT COUNT(*) FROM ".self::tableName()." WHERE layanan_id = :layanan",[':layanan'=>$this->layanan_data->id])->queryScalar();
            if($this->reg_data->debitur_detail_kode==Yii::$app->params['bpjs']['app']['id']){ //jika bpjs, delete tarif tindakan
                if($count>0){
                    foreach($tarif_registrasi as $id){
                        Yii::$app->db->createCommand()->update(self::tableName(),['is_deleted'=>1],['layanan_id'=>$this->layanan_data->id,'tarif_tindakan_id'=>$id])->execute();
                    }
                }
            }else{
                $data=[];
                foreach($tarif_registrasi as $id){
                    $tarif=Yii::$app->db->createCommand("SELECT (js_adm+js_sarana+js_bhp) as total FROM ".TarifTindakan::tableName()." WHERE id = :id")->bindValues([':id'=>$id])->queryOne();
                    $data[]=[
                        $this->layanan_data->id,
                        $id,
                        $this->reg_data->tgl_masuk,
                        1,
                        $tarif['total'],
                        $tarif['total'],
                        date('Y-m-d H:i:s'),
                        AuthUser::user()->id,
                    ];
                }
                $this->tarif_tindakan_data=$data;
                if($count<1){ //jika tarif belum ada, insert
                    return Yii::$app->db->createCommand()->batchInsert(self::tableName(), ['layanan_id', 'tarif_tindakan_id','tanggal','jumlah','harga','subtotal','created_at','created_by'],$data)->execute();
                }
            }
        }
        return true;
    }
}