<?php

namespace app\models;

use app\models\Event;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * EventSearch.
 */
class EventSearch extends Event
{
    public function rules() {
        return [
            [['name', 'address'], 'string', 'max' => 50],
            [['creation_date', 'start_date', 'end_date'], 'safe'],
        ];
    }

    /**
     * Search
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params, $user_id)
    {
        $query = Event::find();

        // create data provider
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // enable sorting for the related columns
/*        $addSortAttributes = ["profile.full_name"];
        foreach ($addSortAttributes as $addSortAttribute) {
            $dataProvider->sort->attributes[$addSortAttribute] = [
                'asc'   => [$addSortAttribute => SORT_ASC],
                'desc'  => [$addSortAttribute => SORT_DESC],
            ];
        }*/
        if ($user_id != '') {
            $query->andFilterWhere(['=', 'user_id', Yii::$app->user->id]);
        }

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'creation_date', $this->creation_date])
            ->andFilterWhere(['like', 'start_date', $this->start_date])
            ->andFilterWhere(['like', 'end_date', $this->end_date]);

        return $dataProvider;
    }

    public function afterValidate() {

    }
}