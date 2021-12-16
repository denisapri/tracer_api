<?php
namespace app\models;
use Yii;
class JenisIdentitas extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'pendaftaran.jenis_identitas';
    }
    public function rules()
    {
        return [
            [['kode', 'nama', 'aktif'], 'required'],
            [['aktif', 'created_by', 'updated_by', 'is_deleted'], 'default', 'value' => null],
            [['aktif', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['kode'], 'string', 'max' => 10],
            [['nama'], 'string', 'max' => 255],
            [['kode'], 'unique'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'kode' => 'Kode',
            'nama' => 'Nama',
            'aktif' => 'Aktif',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
        ];
    }
    public static function find()
    {
        return new BaseQuery(get_called_class());
    }
    static function allAktif()
    {
        return self::find()->where('aktif=:aktif and deleted_at is null',[':aktif'=>1])->asArray()->all();
    }
}