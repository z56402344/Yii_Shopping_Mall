<?php

namespace app\modules\models;

use Yii;
use yii\db\ActiveRecord;
use yii\swiftmailer\Mailer;

class Admin extends ActiveRecord{

    public $rememberMe = true;
    public $repass;
    public static function tableName(){
        return "{{%admin}}";
    }

    public function rules(){

        return [
            ['adminuser','required', 'message'=>'管理员账号不能为空', 'on'=>['login','seekpass', 'changepass']],
            ['adminpass','required', 'message'=>'管理员密码不能为空', 'on'=> ['login','changepass'] ],
            ['rememberMe','boolean', 'on'=> 'login'],
            ['adminpass','validatePass', 'on'=> 'login'],
            //validatePass 为验证的方法
            ['adminemail','required', 'message'=>'管理员邮箱不能为空', 'on'=> 'seekpass'],
            ['adminemail','email', 'message'=>'管理员邮箱格式不正确', 'on'=> 'seekpass'],
            ['adminemail','validateEmail', 'on'=> 'seekpass'],

            ['repass', 'required', 'message' => '确认密码不能为空', 'on' => 'changepass'],
            ['repass', 'compare', 'compareAttribute' => 'adminpass', 'message' => '两次密码输入不一致', 'on' => 'changepass'],

//            ['adminemail', 'unique', 'message' => '电子邮箱已被注册', 'on' => ['adminadd', 'changeemail']],
//            ['adminuser', 'unique', 'message' => '管理员已被注册', 'on' => 'adminadd'],


        ];
    }

    public function validatePass(){
        if (!$this->hasErrors()){
            $data = self::find()->where('adminuser = :user and adminpass = :pass',[":user"=>$this->adminuser, ":pass"=> md5($this->adminpass)])->one();
            if (is_null($data)){
                $this->addError("adminpass","用户名或者密码错误");
            }
        }
    }

    public function validateEmail(){
        if (!$this->hasErrors()) {
            $data = self::find()->where('adminuser = :user and adminemail = :email', [':user' => $this->adminuser, ':email' => $this->adminemail])->one();
            if (is_null($data)) {
                $this->addError("adminemail", "管理员电子邮箱不匹配");
            }
        }
    }

    public function login($data){

        $this->scenario = "login";
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

    public function seekPass($data){
        $this->scenario = "seekpass";
        if ($this->load($data) && $this->validate()){
            //验证成功发送邮件
            $time = time();
            $token = $this->createToken($data['Admin']['adminuser'], $time);
            $mailer = Yii::$app->mailer->compose('seekpass', ['adminuser' => $data['Admin']['adminuser'], 'time' => $time, 'token' => $token]);
            $mailer->setFrom("imooc_shop@163.com");
            $mailer->setTo($data['Admin']['adminemail']);
            $mailer->setSubject("慕课商城-找回密码");
            if ($mailer->send()) {
                return true;
            }
        }
        return false;
    }

    public function createToken($adminuser, $time){
        return md5(md5($adminuser).base64_encode(Yii::$app->request->userIP).md5($time));
    }

    public function changePass($data){
        $this->scenario = "changepass";
        if ($this->load($data) && $this->validate()) {
            var_dump($data);
            var_dump("验证中");
            return (bool)$this->updateAll(['adminpass' => md5($this->adminpass)], 'adminuser = :user', [':user' => $this->adminuser]);
        }
        var_dump("验证失败");
        return false;
    }
}