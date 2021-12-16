<?php
namespace app\models;
use Yii;
class KamarPelayanan extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'medis.kamar_pelayanan';
    }
    public function rules()
    {
        return [
            [['kamar_id', 'dm_sdm_jenis_id', 'aktif'], 'default', 'value' => null],
            [['kamar_id', 'dm_sdm_jenis_id', 'aktif'], 'integer'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'kamar_id' => 'Kamar ID',
            'dm_sdm_jenis_id' => 'Dm Sdm Jenis ID',
            'aktif' => 'Aktif',
        ];
    }
    function getKamar()
    {
        return $this->hasOne(Kamar::className(),['id'=>'kamar_id']);
    }
    function getJenis()
    {
        return $this->hasOne(DmSdmJenis::className(),['kode'=>'dm_sdm_jenis_id']);
    }
}