<?php
namespace app\models;
use Yii;
class DmSdmJenis extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'pegawai.dm_sdm_jenis';
    }
    public function rules()
    {
        return [
            [['nama', 'kode_rumpun', 'kode_sub_rumpun'], 'required'],
            [['kode_rumpun', 'kode_sub_rumpun', 'aktif', 'is_deleted'], 'default', 'value' => null],
            [['kode_rumpun', 'kode_sub_rumpun', 'aktif', 'is_deleted'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['created_by', 'updated_by'], 'string'],
            [['nama'], 'string', 'max' => 80],
            [['kode_sub_rumpun'], 'exist', 'skipOnError' => true, 'targetClass' => DmSdmSubRumpun::className(), 'targetAttribute' => ['kode_sub_rumpun' => 'kode']],
        ];
    }
    public function attributeLabels()
    {
        return [
            'kode' => 'Kode',
            'nama' => 'Nama',
            'kode_rumpun' => 'Kode Rumpun',
            'kode_sub_rumpun' => 'Kode Sub Rumpun',
            'aktif' => 'Aktif',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'is_deleted' => 'Is Deleted',
        ];
    }
    public function getKodeSubRumpun()
    {
        return $this->hasOne(DmSdmSubRumpun::className(), ['kode' => 'kode_sub_rumpun']);
    }
    static function jenisPelayanan()
    {
        $result=self::find()->select('kode, nama')->where(['in','kode_sub_rumpun',[1,2,3,4]])->asArray()->all();
        $result=array_map(function($q){
            return ['id'=>$q['kode'],'text'=>trim(str_replace('SPESIALIS','',str_replace('DOKTER','',$q['nama'])))];
        },$result);
        return $result;
    }
}
