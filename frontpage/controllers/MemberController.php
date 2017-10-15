<?php

namespace app\controllers;

use app\models\User;
use Yii;
use yii\web\Controller;

class MemberController extends Controller{

    public function actionAuth(){
        $this->layout = "layout2";
        if (Yii::$app->request->isGet) {
            $url = Yii::$app->request->referrer;
            if (empty($url)) {
                $url = "/";
            }
            Yii::$app->session->setFlash('referrer', $url);
        }
        $model = new User();
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if ($model->login($post)) {
                $url = Yii::$app->session->getFlash('referrer');
                var_dump('$url = '.$url);
//                return $this->redirect($url);
                return $this->render("auth", ['model' => $model]);
            }
        }
        return $this->render("auth", ['model' => $model]);
    }

    public function actionReg(){
        $this->layout = "layout2";
        $model = new User();
        if (Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            if ($model->regByMail($post)){
                Yii::$app->session->setFlash('info','注册成功,请到邮箱查收你的邮件');
            }
        }
        return $this->render('auth',['model'=>$model]);
    }


}