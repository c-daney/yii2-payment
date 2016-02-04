<?php

namespace lubaogui\payment\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use lubaogui\payment\models\PayableProcessBatch;

/**
 * PayableProcessBatchSearch represents the model behind the search form about `common\models\Device`.
 */
class PayableProcessBatchSearch extends PayableProcessBatch 
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'admin_uid', 'status', 'created_at', 'updated_at'], 'integer'],
            [['admin_username'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = PayableProcessBatch::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'admin_uid' => $this->admin_uid,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'admin_username', $this->admin_username]);

        return $dataProvider;
    }
}
