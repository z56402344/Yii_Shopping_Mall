<?php

namespace app\modules\controllers;

use app\models\Profile;
use app\models\User;
use yii\base\Exception;
use yii\data\Pagination;
use Yii;
use yii\web\Controller;

class UserController extends Controller{

    public function actionMailchangepass(){

        $this->layout = false;
        $time = Yii::$app->request->get("timestamp");
        $adminuser = Yii::$app->request->get("adminuser");
        $token = Yii::$app->request->get("token");
        $model = new User();
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

    public function actionUsers(){
        $this->layout = "layout1";
        $model = User::find()->joinWith('profile');
        $count = $model->count();
        $pageSize = Yii::$app->params['pageSize']['user'];
        $pager = new Pagination(['totalCount'=> $count, 'pageSize' => $pageSize]);
        $users = $model->offset($pager->offset)->limit($pager->limit)->all();
        return $this->render("users",['users'=>$users, 'pager'=>$pager]);
    }

    public function actionReg(){
        $this->layout = "layout1";
        $model = new User();
        if (Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            if ($model->reg($post)){
                Yii::$app->session->setFlash('info','添加成功');
            }else{
                Yii::$app->session->setFlash('info','添加失败');
            }
        }
        $model->userpass = '';
        $model->repass = '';
        return $this->render('reg',['model' =>$model]);
    }

    public function actionDel(){
        try{
            $userid = Yii::$app->request->get('userid');
            if (empty($userid)){
                throw new Exception();
//                $this->redirect(['user/users']);
            }
            $trans =Yii::$app->db->beginTransaction();
            if ($obj = Profile::find()->where('userid = :id', [':id' => $userid])->one()) {
                $res = Profile::deleteAll('userid = :id', [':id' => $userid]);
                if (empty($res)) {
                    throw new \Exception();
                }
            }
            $model = new User();
            if ($model->deleteAll('userid = :id',[':id' => $userid])){
                Yii::$app->session->setFlash('info','删除成功');
                $this->redirect(['user/users']);
            }
            $trans->commit();
        }catch (Exception $e){
            if (Yii::$app->db->getTransaction()){
                $trans->rollBack();
            }
        }

    }

    public function actionChangeemail(){
        $this->layout = 'layout1';
        $model = Admin::find()->where('adminuser = :user',[':user'=>Yii::$app->session['admin']['adminuser']])->one();
        if (Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            if ($model->changeemail($post)){
                Yii::$app->session->setFlash('修改成功');
            }
        }
        $model->adminpass = '';
        return $this->render('changeemail',['model'=>$model]);
    }

    public function actionChangepass(){
        $this->layout = 'layout1';
        $model = User::find()->where('adminuser = :user',[':user' => Yii::$app->session['admin']['adminuser']])->one();
        if (Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            if ($model->changepass($post)){
                Yii::$app->session->setFlash('info','修改成功');
            }
        }
        $model->adminpass = '';
        $model->repass = '';
        return $this->render('changepass',['model'=>$model]);
    }

}