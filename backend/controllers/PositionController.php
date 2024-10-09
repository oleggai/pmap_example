<?php


use common\models\position\Position;
use common\models\position\SearchPosition;
use common\models\User;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class PositionController extends Controller
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['get'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['index', 'create', 'update', 'delete', 'view'],
                        'allow' => true,
                        'roles' => [User::PERMISSION_MANAGE_POSITIONS]
                    ]
                ]
            ]
        ];
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SearchPosition();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $position = new Position();

        if($position->load(\Yii::$app->request->post()) && $position->save()) {
            \Yii::$app->session->setFlash('success', 'Посада успішно створена');
            return $this->redirect(['position/view', 'id' => $position->id]);
        }

        return $this->render('create', [
            'position' => $position
        ]);
    }

    /**
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        /* @var $position Position */
        $position = $this->findModel($id);

        if($position->load(\Yii::$app->request->post()) && $position->save()) {
            return $this->redirect(['position/view', 'id' => $position->id]);
        }

        return $this->render('update', [
            'position' => $position
        ]);
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        /* @var $position Position */
        $position = $this->findModel($id);

        if($position->delete()) {
            \Yii::$app->session->setFlash('success', 'Посада успішно видалена');
        }

        return $this->redirect(['position/index']);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $position = $this->findModel($id);

        return $this->render('view', [
            'position' => $position
        ]);
    }

    /**
     * @param $id
     * @return null|static
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Position::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @param null $q
     * @return array
     */
    public function actionGet($q = null)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $q = strtolower($q);
            $data = Position::find()->select([
                'id',
                'name as text'])
                ->where(['like', 'LOWER(name)', $q.'%', false])
                ->asArray()->all();

            $data = array_merge([[
                'id' => $q,
                'text' => $q
            ]], $data);
            $res = [];
            foreach ($data as $row) {
                $res[] = [
                    'id' => $row['text'],
                    'text' => $row['text'],
                ];
            }

            $out['results'] = $res;
        }
        return $out;
    }
}