<?php
namespace app\models;
use Yii;
class KodeRm extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'pendaftaran_m_kode_rm';
    }
    public function rules()
    {
        return [
            [['krm_debitur_detail_id', 'krm_kode'], 'required'],
            [['krm_created_at'], 'safe'],
            [['krm_created_by'], 'integer'],
            [['krm_debitur_detail_id'], 'string', 'max' => 10],
            [['krm_kode'], 'string', 'max' => 5],
        ];
    }
    public function attributeLabels()
    {
        return [
            'krm_id' => 'Krm ID',
            'krm_debitur_detail_id' => 'Krm Debitur Detail ID',
            'krm_kode' => 'Krm Kode',
            'krm_created_at' => 'Krm Created At',
            'krm_created_by' => 'Krm Created By',
        ];
    }
    static function maxKode($debitur=NULL)
    {
        $query = self::find();
        if($debitur!=NULL){
            $query->where(['krm_debitur_detail_id'=>$debitur]);
        }else{ //umum
            $query->where(['krm_debitur_detail_id'=>1001]);
        }
        return array_column($query->select('krm_kode')->asArray()->all(),'krm_kode');
    }
}