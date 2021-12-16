<?php
namespace app\models;
use Yii;
class Negara extends \yii\db\ActiveRecord
{
    static $prefix="ngr";
    public static function tableName()
    {
        return 'sdm_m_negara';
    }
    public function rules()
    {
        return [
            [['ngr_kode', 'ngr_nama', 'ngr_aktif'], 'required'],
            [['ngr_aktif', 'ngr_created_by', 'ngr_updated_by', 'ngr_deleted_by'], 'integer'],
            [['ngr_created_at', 'ngr_updated_at', 'ngr_deleted_at'], 'safe'],
            [['ngr_kode', 'ngr_nama'], 'string', 'max' => 30],
        ];
    }
    public function attributeLabels()
    {
        return [
            'ngr_id' => 'Ngr ID',
            'ngr_kode' => 'Ngr Kode',
            'ngr_nama' => 'Ngr Nama',
            'ngr_aktif' => 'Ngr Aktif',
            'ngr_created_at' => 'Ngr Created At',
            'ngr_created_by' => 'Ngr Created By',
            'ngr_updated_at' => 'Ngr Updated At',
            'ngr_updated_by' => 'Ngr Updated By',
            'ngr_deleted_at' => 'Ngr Deleted At',
            'ngr_deleted_by' => 'Ngr Deleted By',
        ];
    }
    public static function find()
    {
        return new BaseQuery(get_called_class());
    }
    static function all()
    {
        return self::find()->active(self::$prefix)->notDeleted(self::$prefix)->orderBy(['ngr_nama'=>SORT_ASC])->asArray()->all();
    }
}