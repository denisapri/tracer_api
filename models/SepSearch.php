<?php
namespace app\models;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Rujukan;
class SepSearch extends Sep
{
    public $status;
    public function rules()
    {
        return [
            [['sep_id', 'sep_created_by', 'sep_updated_by','sep_is_poli_eksekutif','sep_is_kontrol_post_ri','sep_deleted_at'], 'integer'],
            [['sep_no_kartu','sep_pasien_kode', 'sep_no_sep', 'sep_tgl_rujukan','sep_tgl_sep', 'sep_poli_nama', 'sep_jenis_pelayanan', 'sep_catatan', 'sep_diagnosa_nama','sep_dpjp_nama','sep_skdp_no_surat'], 'safe'],
        ];
    }
    public function scenarios()
    {
        return Model::scenarios();
    }
    public function search($params)
    {
        $query = Sep::find()->orderBy(['sep_tgl_sep'=>SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        if(!$this->validate()){
            return $dataProvider;
        }
        if($this->sep_deleted_at==1){
            $query->where('sep_deleted_at is null');
        }elseif($this->sep_deleted_at==2){
            $query->where('sep_deleted_at is not null');
        }
        $query->andFilterWhere([
            'sep_hak_kelas' => $this->sep_hak_kelas,
            'sep_kelas_rawat' => $this->sep_kelas_rawat,
            'sep_is_bridging' => $this->sep_is_bridging,
            'sep_is_kontrol_post_ri' => $this->sep_is_kontrol_post_ri,
            'sep_is_poli_eksekutif'=>$this->sep_is_poli_eksekutif,
        ]);

        $query->andFilterWhere(['like', 'sep_pasien_kode', $this->sep_pasien_kode])
            ->andFilterWhere(['like', 'sep_no_sep', $this->sep_no_sep])
            ->andFilterWhere(['like', 'sep_no_rujukan', $this->sep_no_rujukan])
            ->andFilterWhere(['like', 'sep_no_kartu', $this->sep_no_kartu])
            ->andFilterWhere(['like', "DATE_FORMAT(sep_tgl_rujukan,'%Y-%m-%d')", $this->sep_tgl_rujukan!=NULL ? date('Y-m-d',strtotime($this->sep_tgl_rujukan)) : NULL])
            ->andFilterWhere(['like', "DATE_FORMAT(sep_tgl_sep,'%Y-%m-%d')", $this->sep_tgl_sep!=NULL ? date('Y-m-d',strtotime($this->sep_tgl_sep)) : NULL])
            ->andFilterWhere(['like', 'sep_poli_nama', $this->sep_poli_nama])
            ->andFilterWhere(['like', 'sep_diagnosa_nama', $this->sep_diagnosa_nama])
            ->andFilterWhere(['like', 'sep_dpjp_nama', $this->sep_dpjp_nama])
            ->andFilterWhere(['like', 'sep_asal_rujukan_nama', $this->sep_asal_rujukan_nama])
            ->andFilterWhere(['like', 'sep_catatan', $this->sep_catatan])
            ->andFilterWhere(['like', 'sep_skdp_no_surat', $this->sep_skdp_no_surat]);

        return $dataProvider;
    }
}