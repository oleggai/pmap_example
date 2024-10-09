<?php

namespace frontend\modules\api\models;

use common\models\ato\Ato;
use common\models\TypeDocument;
use yii\data\ActiveDataFilter;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;

/**
 * Class Document
 * @package frontend\modules\api\models
 *
 * @property integer $id
 * @property string $uuid
 * @property TypeDocument $typeDocument
 * @property Ato $ato
 * @property string $date_approval
 * @property string $decision_number
 */
class Document extends ActiveRecord
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['date_approval', 'decision_number', 'document_type', 'koatuu', 'ato_name', 'ato_type', 'uuid', 'updated_at'], 'safe']
        ];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return array_merge(parent::attributes(), [
            'document_type',
            'koatuu',
            'ato_name',
            'ato_type'
        ]);
    }

    /**
     * @return array
     */
    public function fields()
    {
        return [
            'uuid',
            'ato_type' => function() {
                return $this->ato->type_ato;
            },
            'ato_name' => function() {
                return $this->ato->name;
            },
            'koatuu' => function() {
                return $this->ato->koatuu;
            },
            'document_type' => function() {
                return $this->typeDocument->name;
            },
            'name',
            'date_approval',
            'decision_number',
            'link_decision',
            'link_text',
            'link_graphic',
            'percentage_coverage',
            'info_territory',
            'zoning_plan',
            'additional_info',
            'updated_at'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAto()
    {
        return $this->hasOne(Ato::class, ['id' => 'id_ato']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTypeDocument()
    {
        return $this->hasOne(TypeDocument::class, ['id' => 'id_type_documentation']);
    }

    /**
     * @return ActiveDataFilter|ActiveDataProvider
     */
    public function search()
    {
        $limit = \Yii::$app->request->get('limit');
        $limitRange = [
            'min' => 20,
            'max' => 500
        ];
        $limit = $limit ? : $limitRange['min'];
        $limit = $limit > $limitRange['max'] ? $limitRange['max'] : $limit;

        $query = Document::find()
            ->joinWith(['typeDocument', 'ato']);

        $filter = new ActiveDataFilter([
            'searchModel' => 'frontend\modules\api\models\Document',
            'attributeMap' => [
                'koatuu' => Ato::tableName().'.koatuu',
                'ato_name' => Ato::tableName().'.name',
                'updated_at' => self::tableName().'.updated_at',
                'document_type' => TypeDocument::tableName().'.name',
                'date_approval' => \common\models\document\Document::tableName().'.date_approval',
                'ato_type' => Ato::tableName().'.type_ato'
            ]
        ]);

        $filterCondition = null;

        $get = \Yii::$app->request->get();

        $getParams = [];
        if(key_exists('filter', $get)) {
            $getParams['filter'] = $get['filter'];
        }

        // You may load filters from any source. For example,
        // if you prefer JSON in request body,
        // use Yii::$app->request->getBodyParams() below:
        if ($filter->load($getParams)) {
            $filterCondition = $filter->build();
            if ($filterCondition === false) {
                // Serializer would get errors out of it
                return $filter;
            }
        }

        if ($filterCondition !== null) {
            $query->andFilterWhere($filterCondition);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $limit
            ]
        ]);

        return $dataProvider;
    }
}