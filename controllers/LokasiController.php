<?php

namespace app\controllers;

use Yii;
use app\models\Lokasi;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LokasiController implements the CRUD actions for Lokasi model.
 */
class LokasiController extends Controller
{
    public $layout = 'admin';
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Lokasi models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Lokasi::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Lokasi model.
     * @param int $lokasi_id Lokasi ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($lokasi_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($lokasi_id),
        ]);
    }

    /**
     * Creates a new Lokasi model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Lokasi();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $model->created_by = Yii::$app->user->id;
                if ($model->save()) {
                    return $this->redirect(['view', 'lokasi_id' => $model->lokasi_id]);
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Lokasi model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $lokasi_id Lokasi ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($lokasi_id)
    {
        $model = $this->findModel($lokasi_id);

        if ($this->request->isPost && $model->load($this->request->post())) {
            $model->updated_by = Yii::$app->user->id;
            if ($model->save()) {
                return $this->redirect(['view', 'lokasi_id' => $model->lokasi_id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Lokasi model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $lokasi_id Lokasi ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($lokasi_id)
    {
        $this->findModel($lokasi_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Lokasi model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $lokasi_id Lokasi ID
     * @return Lokasi the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($lokasi_id)
    {
        if (($model = Lokasi::findOne(['lokasi_id' => $lokasi_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
