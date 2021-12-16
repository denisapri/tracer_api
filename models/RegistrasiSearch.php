<?php
namespace app\models;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Registrasi;
class RegistrasiSearch extends Registrasi
{
    public function rules()
    {
        return [
            [['nama','unit','reg_kode', 'reg_pasien_kode', 'reg_tgl_masuk', 'reg_pmdd_kode'], 'safe'],
            [['reg_created_by', 'reg_updated_by', 'reg_deleted_by'], 'integer'],
        ];
    }
    public function scenarios()
    {
        return Model::scenarios();
    }
    public function search($params)
    {
        $query = Registrasi::find()->notDeleted(Registrasi::$prefix)->joinWith([
            'pasien',
            'debiturdetail',
            'layanan'=>function($q){
                $q->where(['or',['pl_jenis_layanan'=>Registrasi::$igd],['pl_jenis_layanan'=>Registrasi::$rj]])->joinWith([
                    'unit'
                ],false);
            }
        ],false)->select('ps_nama as nama,reg_tgl_masuk,reg_kode,reg_pasien_kode,pmdd_nama as debitur,unt_nama as unit')->orderBy(['reg_tgl_masuk'=>SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->reg_tgl_masuk=date('d-m-Y');
        $this->load($params);
        if(!$this->validate()){
            return $dataProvider;
        }
        $query->andFilterWhere(['like','ps_nama', $this->nama])
            ->andFilterWhere(['like','reg_kode', $this->reg_kode])
            ->andFilterWhere(['like', 'reg_pasien_kode', $this->reg_pasien_kode])
            ->andFilterWhere(['like', 'reg_pmdd_kode', $this->reg_pmdd_kode])
            ->andFilterWhere(['like', "DATE_FORMAT(reg_tgl_masuk,'%Y-%m-%d')", $this->reg_tgl_masuk!=NULL ? date('Y-m-d',strtotime($this->reg_tgl_masuk)) : NULL]);
        return $dataProvider;
    }
}