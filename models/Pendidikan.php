<?php
namespace app\models;
use Yii;
class Pendidikan extends \yii\db\ActiveRecord
{
    static $prefix="pdd";
    public static function tableName()
    {
        return 'sdm_m_pendidikan';
    }
    public function rules()
    {
        return [
            [['pdd_nama'], 'string', 'max' => 100],
            [['pdd_aktif'], 'integer', 'max' => 4],
            [['pdd_created_by','pdd_updated_by','pdd_deleted_by'],'integer'],
            [['pdd_created_at', 'pdd_updated_at','pdd_deleted_at'], 'safe'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'pdd_id' => 'Kode',
            'pdd_nama'=>'Nama',
            'pdd_aktif' => 'Aktif',
            'pdd_created_at' => 'Created At',
            'pdd_created_by' => 'Created By',
            'pdd_updated_at' => 'Updated At',
            'pdd_updated_by' => 'Updated By',
            'pdd_deleted_at'=>'pdd_deleted_at',
            'pdd_deleted_by'=>'pdd_deleted_by',
        ];
    }
    public static function find()
    {
        return new BaseQuery(get_called_class());
    }
    static function all()
    {
        return self::find()->active(self::$prefix)->orderBy(['pdd_nama'=>SORT_ASC])->asArray()->all();
    }
}