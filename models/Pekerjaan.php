<?php
namespace app\models;
use Yii;
class Pekerjaan extends \yii\db\ActiveRecord
{
    static $prefix="pkj";
    public static function tableName()
    {
        return 'sdm_m_pekerjaan';
    }
    public function rules()
    {
        return [
            [['pkj_aktif'], 'required'],
            [['pkj_aktif', 'pkj_created_by', 'pkj_updated_by', 'pkj_deleted_by'], 'integer'],
            [['pkj_created_at', 'pkj_updated_at', 'pkj_deleted_at'], 'safe'],
            [['pkj_nama'], 'string', 'max' => 100],
        ];
    }
    public function attributeLabels()
    {
        return [
            'pkj_id' => 'Pkj ID',
            'pkj_nama' => 'Pkj Nama',
            'pkj_aktif' => 'Pkj Aktif',
            'pkj_created_at' => 'Pkj Created At',
            'pkj_created_by' => 'Pkj Created By',
            'pkj_updated_at' => 'Pkj Updated At',
            'pkj_updated_by' => 'Pkj Updated By',
            'pkj_deleted_at' => 'Pkj Deleted At',
            'pkj_deleted_by' => 'Pkj Deleted By',
        ];
    }
    public static function find()
    {
        return new BaseQuery(get_called_class());
    }
    static function all()
    {
        return self::find()->active(self::$prefix)->orderBy(['pkj_nama'=>SORT_ASC])->asArray()->all();
    }
}