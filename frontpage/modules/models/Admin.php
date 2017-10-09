<?php

namespace app\modules\models;

use Yii;
use yii\db\ActiveRecord;

class Admin extends ActiveRecord{

    public $rememberMe = true;
    public static function tableName(){
        return "{{%admin}}";
    }

    public function rules(){

        return [
            ['adminuser','required', 'message'=>'管理员账号不能为空'],
            ['adminpass','required', 'message'=>'管理员密码不能为空'],
            ['rememberMe','boolean'],
            //validatePass 为验证的方法
            ['adminpass','validatePass'],

        ];
    }

    public function validatePass(){
        if ($this->hasErrors()){
            $data = self::find()->where('adminuser = :user and adminpass = :pass',[":user"=>$this->adminuser, ":pass"=> md5($this->adminpass)])->one();
            if (is_null($data)){
                $this->addError("adminpass","用户名或者密码错误");
            }
        }
    }

    public function login($data){

        if ($this->load($data) && $this->validate()){
            //加载成功同时验证成功
            //记住我的有效期
            $lifetime = $this->rememberMe ?24*3600:0;
            $session = Yii::$app->session;
            session_set_cookie_params($lifetime);
            $session['admin'] = [
              'adminuser'=>$this->adminuser,
                'isLogin' => 1,
            ];
            $this->updateAll([
                'logintime'=>time(),
                'loginip'=>ip2long(Yii::$app->request->userIP)],'adminuser = :user',[':user'=>$this->adminuser
            ]);
            return (bool)$session['admin']['isLogin'];
        }
        return false;
    }
}