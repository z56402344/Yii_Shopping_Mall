<?php

namespace app\modules\controllers;

use app\modules\models\Admin;
use yii\data\Pagination;
use yii\web\Controller;
use Yii;

class ManageController extends Controller{

    public function actionMailchangepass(){

        $this->layout = false;
        $time = Yii::$app->request->get("timestamp");
        $adminuser = Yii::$app->request->get("adminuser");
        $token = Yii::$app->request->get("token");
        $model = new Admin();
        $myToken = $model->createToken($adminuser,$time);
        if ($token != $myToken){
            //token 不相等
            $this->redirect(['public/login']);
            Yii::$app->end();
        }
        if (time()- $time > 10 * 1000 * 60){
            $this->redirect(['public/login']);
            Yii::$app->end();
        }
        if (Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            if ($model->changePass($post)){
                var_dump("验证成功");
                Yii::$app->session->setFlash('info','密码修改成功');
            }
        }
        var_dump("验证失败了2");

        $model->adminuser = $adminuser;
        return $this->render("mailchangepass",['model'=>$model]);
    }

    public function actionManagers(){

        $this->layout = "layout1";
        $model = Admin::find();
        $count = $model->count();
        $pageSize = Yii::$app->params['pageSize']['manage'];
        $pager = new Pagination(['totalCount'=> $count, 'pageSize' => $pageSize]);
        $managers = $model->offset($pager->offset)->limit($pager->limit)->all();
        return $this->render("managers",['managers'=>$managers, 'pager'=>$pager]);
    }
}