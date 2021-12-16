<?php
namespace app\models;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\SepPengajuan;
class SepPengajuanSearch extends SepPengajuan
{
    public function rules()
    {
        return [
            [['psp_id', 'psp_created_by', 'psp_approved_by'], 'integer'],
            [['psp_pasien_kode', 'psp_no_kartu', 'psp_tgl_sep', 'psp_jenis_pelayanan', 'psp_ket_pengajuan', 'psp_status', 'psp_ket_approval', 'psp_created_at', 'psp_approved_at'], 'safe'],
        ];
    }
    public function scenarios()
    {
        return Model::scenarios();
    }
    public function search($params)
    {
        $query = SepPengajuan::find()->orderBy(['psp_created_at'=>SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'psp_jenis_pelayanan' => $this->psp_jenis_pelayanan,
            'psp_status' => $this->psp_status,
        ]);
        $query->andFilterWhere(['like', 'psp_pasien_kode', $this->psp_pasien_kode])
            ->andFilterWhere(['like', 'psp_no_kartu', $this->psp_no_kartu])
            ->andFilterWhere(['like', "DATE_FORMAT(psp_tgl_sep,'%Y-%m-%d')", $this->psp_tgl_sep!=NULL ? date('Y-m-d',strtotime($this->psp_tgl_sep)) : NULL])
            ->andFilterWhere(['like', 'psp_ket_pengajuan', $this->psp_ket_pengajuan])
            ->andFilterWhere(['like', 'psp_ket_approval', $this->psp_ket_approval]);
        return $dataProvider;
    }
}