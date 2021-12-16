<?php
namespace app\models;
use Yii;
class Debitur extends \yii\db\ActiveRecord
{
    static $prefix="pmd";
    public static function tableName()
    {
        return 'pendaftaran_m_debitur';
    }
    public function rules()
    {
        return [
            [['pmd_kode', 'pmd_nama'], 'required'],
            [['pmd_aktif', 'pmd_created_by', 'pmd_updated_by', 'pmd_deleted_by'], 'integer'],
            [['pmd_created_at', 'pmd_updated_at', 'pmd_deleted_at'], 'safe'],
            [['pmd_kode'], 'string', 'max' => 10],
            [['pmd_nama'], 'string', 'max' => 255],
            [['pmd_kode'], 'unique'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'pmd_kode' => 'Pmd Kode',
            'pmd_nama' => 'Pmd Nama',
            'pmd_aktif' => 'Pmd Aktif',
            'pmd_created_at' => 'Pmd Created At',
            'pmd_created_by' => 'Pmd Created By',
            'pmd_updated_at' => 'Pmd Updated At',
            'pmd_updated_by' => 'Pmd Updated By',
            'pmd_deleted_at' => 'Pmd Deleted At',
            'pmd_deleted_by' => 'Pmd Deleted By',
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