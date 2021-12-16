<?php
namespace app\models;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Rujukan;
class RujukanSearch extends Rujukan
{
    public function rules()
    {
        return [
            [['ruj_id', 'ruj_created_by', 'ruj_updated_by'], 'integer'],
            [['ruj_pasien_kode', 'ruj_no_sep', 'ruj_tgl_rujukan', 'ruj_ppk_dirujuk_kode', 'ruj_jenis_pelayanan', 'ruj_catatan', 'ruj_diagnosa_kode', 'ruj_tipe_rujukan', 'ruj_poli_kode','ruj_ppk_dirujuk_nama','ruj_poli_nama'], 'safe'],
        ];
    }
    public function scenarios()
    {
        return Model::scenarios();
    }
    public function search($params)
    {
        $query = Rujukan::find()->orderBy(['ruj_tgl_rujukan'=>SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'ruj_jenis_pelayanan' => $this->ruj_jenis_pelayanan,
            'ruj_tipe_rujukan' => $this->ruj_tipe_rujukan,
        ]);

        $query->andFilterWhere(['ilike', 'ruj_pasien_kode', $this->ruj_pasien_kode])
            ->andFilterWhere(['ilike', 'ruj_no_sep', $this->ruj_no_sep])
            ->andFilterWhere(['ilike', "DATE_FORMAT(ruj_tgl_rujukan,'%Y-%m-%d')", $this->ruj_tgl_rujukan!=NULL ? date('Y-m-d',strtotime($this->ruj_tgl_rujukan)) : NULL])
            ->andFilterWhere(['ilike', 'ruj_poli_nama', $this->ruj_poli_nama])
            ->andFilterWhere(['ilike', 'ruj_ppk_dirujuk_nama', $this->ruj_ppk_dirujuk_nama]);

        return $dataProvider;
    }
}