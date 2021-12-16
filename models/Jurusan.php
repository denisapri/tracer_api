<?php
namespace app\models;
use Yii;
class Jurusan extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'sdm_m_jurusan';
    }
    public function rules()
    {
        return [
            [['kode', 'kode_jurusan'], 'required'],
            [['kode', 'aktif', 'is_deleted'], 'default', 'value' => null],
            [['kode', 'aktif', 'is_deleted'], 'integer'],
            [['kode_jurusan', 'created_by', 'updated_by'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['nama_jurusan'], 'string', 'max' => 50],
        ];
    }
    public function attributeLabels()
    {
        return [
            'jur_id ' => 'id',
            'jur_kode' => 'Kode Jurusan',
            'jur_nama' => 'Nama Jurusan',
            'jur_aktif' => 'Aktif',
            'jur_created_at' => 'Created At',
            'jur_created_by' => 'Created By',
            'jur_updated_at' => 'Updated At',
            'jur_updated_by' => 'Updated By',
            'jur_deleted_at' => 'Is Deleted',
            'jur_deleted_by'=>'jur_deleted_by'
        ];
    }
    static function all()
    {
        return self::find()->where(['jur_aktif'=>1])->orderBy(['jur_nama'=>SORT_ASC])->asArray()->all();
    }
}
