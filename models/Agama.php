<?php
namespace app\models;
use Yii;
class Agama extends \yii\db\ActiveRecord
{
    static $prefix='agm';
    static $alias='agm';
    public static function tableName()
    {
        return 'sdm_m_agama';
    }
    public function rules()
    {
        return [
            [['agm_created_at', 'agm_updated_at','agm_deleted_at'], 'safe'],
            [['created_by', 'updated_by'], 'string'],
            [['agama'], 'string', 'max' => 30],
            [['agm_aktif','agm_created_by','agm_updated_by','agm_deleted_by'],'integer'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'agm_id' => 'ID',
            'agm_nama' => 'Agama',
            'agm_aktif'=>'Aktif',
            'agm_created_at' => 'Created At',
            'agm_created_by' => 'Created By',
            'agm_updated_at' => 'Updated At',
            'agm_updated_by' => 'Updated By',
            'agm_deleted_at'=>'deleted at',
            'agm_deleted_by'=>'deleted by',
        ];
    }
    static function find()
    {
        return new BaseQuery(get_called_class());
    }
    static function all()
    {
        return self::find()->where(['agm_aktif'=>1])->notDeleted(self::$prefix)->orderBy(['agm_nama'=>SORT_ASC])->asArray()->all();
    }
}
