<?php
namespace app\models;
use Yii;
use app\widgets\AuthUser;
class UnitPenempatan extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'pegawai.dm_unit_penempatan';
    }
    public function rules()
    {
        return [
            [['nama', 'unit_rumpun'], 'required'],
            [['unit_rumpun', 'aktif', 'is_deleted'], 'default', 'value' => null],
            [['unit_rumpun', 'aktif', 'is_deleted'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['created_by', 'updated_by'], 'string'],
            [['nama'], 'string', 'max' => 120],
            [['kode_unitsub_maping_simrs'], 'string', 'max' => 6],
        ];
    }
    public function attributeLabels()
    {
        return [
            'kode' => 'Kode',
            'nama' => 'Nama',
            'unit_rumpun' => 'Unit Rumpun',
            'kode_unitsub_maping_simrs' => 'Kode Unitsub Maping Simrs',
            'aktif' => 'Aktif',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'is_deleted' => 'Is Deleted',
        ];
    }
}