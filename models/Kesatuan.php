<?php
namespace app\models;
use Yii;
class Kesatuan extends \yii\db\ActiveRecord
{
    static $prefix="smk";
    public static function tableName()
    {
        return 'sdm_m_kesatuan';
    }
    public function rules()
    {
        return [
            [['smk_nama'], 'required'],
            [['smk_aktif', 'smk_created_by', 'smk_updated_by', 'smk_deleted_by'], 'integer'],
            [['smk_created_at', 'smk_updated_at', 'smk_deleted_at'], 'safe'],
            [['smk_nama'], 'string', 'max' => 100],
        ];
    }
    public function attributeLabels()
    {
        return [
            'smk_id' => 'Smk ID',
            'smk_nama' => 'Smk Nama',
            'smk_aktif' => 'Smk Aktif',
            'smk_created_at' => 'Smk Created At',
            'smk_created_by' => 'Smk Created By',
            'smk_updated_at' => 'Smk Updated At',
            'smk_updated_by' => 'Smk Updated By',
            'smk_deleted_at' => 'Smk Deleted At',
            'smk_deleted_by' => 'Smk Deleted By',
        ];
    }
    public static function find()
    {
        return new BaseQuery(get_called_class());
    }
    static function all()
    {
        return self::find()->orderBy(['smk_nama'=>SORT_ASC])->active(self::$prefix)->notDeleted(self::$prefix)->asArray()->all();
    }
}
