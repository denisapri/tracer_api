<?php
namespace app\models;
use Yii;
class Kiriman extends \yii\db\ActiveRecord
{
    static $prefix="pmkr";
    public static function tableName()
    {
        return 'pendaftaran_m_kiriman';
    }
    public function rules()
    {
        return [
            [['pmkr_kode', 'pmkr_nama', 'pmkr_aktif'], 'required'],
            [['pmkr_aktif', 'pmkr_created_by', 'pmkr_updated_by', 'pmkr_deleted_by'], 'integer'],
            [['pmkr_created_at', 'pmkr_updated_at', 'pmkr_deleted_at'], 'safe'],
            [['pmkr_kode'], 'string', 'max' => 10],
            [['pmkr_nama'], 'string', 'max' => 255],
            [['pmkr_kode'], 'unique'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'pmkr_kode' => 'Pmkr Kode',
            'pmkr_nama' => 'Pmkr Nama',
            'pmkr_aktif' => 'Pmkr Aktif',
            'pmkr_created_at' => 'Pmkr Created At',
            'pmkr_created_by' => 'Pmkr Created By',
            'pmkr_updated_at' => 'Pmkr Updated At',
            'pmkr_updated_by' => 'Pmkr Updated By',
            'pmkr_deleted_at' => 'Pmkr Deleted At',
            'pmkr_deleted_by' => 'Pmkr Deleted By',
        ];
    }
    public static function find()
    {
        return new BaseQuery(get_called_class());
    }
    static function all()
    {
        return self::find()->active(self::$prefix)->notDeleted(self::$prefix)->asArray()->all();
    }
}
