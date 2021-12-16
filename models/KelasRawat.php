<?php
namespace app\models;
use Yii;
class KelasRawat extends \yii\db\ActiveRecord
{
    static $prefix="kr";
    public static function tableName()
    {
        return 'pendaftaran_m_kelas_rawat';
    }
    public function rules()
    {
        return [
            [['kr_kode', 'kr_nama'], 'required'],
            [['kr_aktif', 'kr_created_by', 'kr_updated_by', 'kr_deleted_by'], 'integer'],
            [['kr_created_at', 'kr_updated_at', 'kr_deleted_at'], 'safe'],
            [['kr_kode'], 'string', 'max' => 3],
            [['kr_nama'], 'string', 'max' => 30],
            [['kr_kode'], 'unique'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'kr_kode' => 'Kr Kode',
            'kr_nama' => 'Kr Nama',
            'kr_aktif' => 'Kr Aktif',
            'kr_created_at' => 'Kr Created At',
            'kr_created_by' => 'Kr Created By',
            'kr_updated_at' => 'Kr Updated At',
            'kr_updated_by' => 'Kr Updated By',
            'kr_deleted_at' => 'Kr Deleted At',
            'kr_deleted_by' => 'Kr Deleted By',
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
