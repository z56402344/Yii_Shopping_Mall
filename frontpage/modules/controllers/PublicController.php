<?php

namespace app\modules\controllers;

use app\modules\models\Admin;
use Yii;
use yii\web\Controller;

class PublicController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionLogin()
    {
        $model = new Admin();
        $this->layout = false;
        if (Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
//            var_dump($post);
            if ($model->login($post)){
                $this->redirect(['default/index']);
                Yii::$app->end();
            }
        }
        return $this->render('login',['model'=>$model]);
    }

    
    public function actionLogout(){
        Yii::$app->session->removeAll();
        if (!isset(Yii::$app->session['admin']['isLogin'])){
            return $this->redirect(['public/login']);
            Yii::$app->end();
        }
        return $this->goBack();
    }
}
