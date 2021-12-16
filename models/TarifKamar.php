<?php
namespace app\models;
use Yii;
class TarifKamar extends \yii\db\ActiveRecord
{
    static $prefix="tkr";
    public static function tableName()
    {
        return 'medis_tarif_kamar';
    }
    public function rules()
    {
        return [
            [['tkr_kmr_id', 'tkr_skt_id', 'tkr_biaya'], 'required'],
            [['tkr_kmr_id', 'tkr_skt_id', 'tkr_biaya', 'tkr_created_by', 'tkr_updated_by', 'tkr_deleted_by'], 'integer'],
            [['tkr_created_at', 'tkr_updated_at', 'tkr_deleted_at'], 'safe'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'tkr_id' => 'Tkr ID',
            'tkr_kmr_id' => 'Tkr Kmr ID',
            'tkr_skt_id' => 'Tkr Skt ID',
            'tkr_biaya' => 'Tkr Biaya',
            'tkr_created_at' => 'Tkr Created At',
            'tkr_created_by' => 'Tkr Created By',
            'tkr_updated_at' => 'Tkr Updated At',
            'tkr_updated_by' => 'Tkr Updated By',
            'tkr_deleted_at' => 'Tkr Deleted At',
            'tkr_deleted_by' => 'Tkr Deleted By',
        ];
    }
}
