<?php
namespace app\models;
use Yii;
class CaraMasukUnit extends \yii\db\ActiveRecord
{
    static $prefix="cmu";
    public static function tableName()
    {
        return 'pendaftaran_m_cara_masuk_unit';
    }
    public function rules()
    {
        return [
            [['cmu_kode', 'cmu_nama', 'cmu_created_by'], 'required'],
            [['cmu_aktif', 'cmu_created_by', 'cmu_updated_by', 'cmu_deleted_by'], 'integer'],
            [['cmu_created_at', 'cmu_updated_at', 'cmu_deleted_at'], 'safe'],
            [['cmu_kode'], 'string', 'max' => 10],
            [['cmu_nama'], 'string', 'max' => 255],
            [['cmu_kode'], 'unique'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'cmu_kode' => 'Cmu Kode',
            'cmu_nama' => 'Cmu Nama',
            'cmu_aktif' => 'Cmu Aktif',
            'cmu_created_at' => 'Cmu Created At',
            'cmu_created_by' => 'Cmu Created By',
            'cmu_updated_at' => 'Cmu Updated At',
            'cmu_updated_by' => 'Cmu Updated By',
            'cmu_deleted_at' => 'Cmu Deleted At',
            'cmu_deleted_by' => 'Cmu Deleted By',
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