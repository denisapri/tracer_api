<?php
namespace app\models;
use Yii;
class Suku extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'sdm_m_suku';
    }
    public function rules()
    {
        return [
            [['suk_created_at', 'suk_updated_at','suk_deleted_at'], 'safe'],
            [['created_by', 'updated_by'], 'string'],
            [['nama'], 'string', 'max' => 30],
            [['suk_created_by','suk_updated_by','suk_deleted_by'],'integer']
        ];
    }
    public function attributeLabels()
    {
        return [
            'suk_id' => 'Kode',
            'suk_nama' => 'Nama',
            'suk_aktif'=>'Aktif',
            'suk_created_at' => 'Created At',
            'suk_created_by' => 'Created By',
            'suk_updated_at' => 'Updated At',
            'suk_updated_by' => 'Updated By',
            'suk_deleted_at'=>'suk_deleted_at',
            'suk_deleted_by'=>'suk_deleted_by',
        ];
    }
    static function all()
    {
        return self::find()->where(['suk_aktif'=>1])->orderBy(['suk_nama'=>SORT_ASC])->asArray()->all();
    }
}
