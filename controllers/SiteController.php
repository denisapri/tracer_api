<?php

namespace app\controllers;

use app\models\Post;
use app\models\User;
use app\models\UserIdentity;
use app\models\Status;
use app\models\Pasien;
use app\models\AccessToken;
use Yii;
use app\models\Registrasi;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ErrorAction;

/*
 * Created on Thu Feb 22 2018
 * By Heru Arief Wijaya
 * Copyright (c) 2018 belajararief.com
 */

class SiteController extends Controller
{    
    protected function verbs()
    {
       return [
           'signup' => ['POST'],
           'login' => ['POST'],
       ];
    }

    public function actionIndex()
    {
        $model = Registrasi::find()->joinWith(['pasien','kirimandetail','debiturdetail'],false)->with([
                'layanan'=>function($q){
                    $q->joinWith(['unit','kamar'])->select(['pl_reg_kode','pl_unit_kode','pl_jenis_layanan','pl_tgl_masuk','pl_tgl_keluar','unt_nama as nama_unit','pl_kamar_id','kmr_no_kamar','kmr_no_kasur'])->orderBy(['pl_tgl_masuk'=>SORT_ASC]);
                }
            ])->select(['reg_kode','reg_kode as no_registrasi','ps_kode as no_rm','ps_nama','reg_tgl_masuk','reg_tgl_keluar','pmkd_nama as kirimandetail','pmdd_nama as debiturdetail'])->where(['reg_kode'=>'1'])->notDeleted(Registrasi::$prefix)->asArray()->limit(1)->one();
            if($model==NULL){
                throw new NotFoundHttpException('The requested page does not exist.');
            }
            return $model;

    }
    public function actionView($id)
    {
        $post = Post::findOne($id);
        return [
            'status' => Status::STATUS_FOUND,
            'message' => 'Data Found',
            'data' => $post
        ];
    }

    public function actionSignup()
    {
        $model = new User();
        $params = Yii::$app->request->post();
        if(!$params) {
            Yii::$app->response->statusCode = Status::STATUS_BAD_REQUEST;
            return [
                'status' => Status::STATUS_BAD_REQUEST,
                'message' => "Need username, password, and email.",
                'data' => ''
            ];
        }


        $model->pgw_username = $params['username'];
        $model->pgw_email = $params['email'];

        $model->setPassword($params['password']);
        $model->generateAuthKey();
        $model->pgw_aktif = User::STATUS_ACTIVE;

        if ($model->save()) {
            Yii::$app->response->statusCode = Status::STATUS_CREATED;
            $response['isSuccess'] = 201;
            $response['message'] = 'You are now a member!';
            $response['user'] = \app\models\User::findByUsername($model->pgw_username);
            return [
                'status' => Status::STATUS_CREATED,
                'message' => 'You are now a member',
                'data' => User::findByUsername($model->username),
            ];
        } else {
            Yii::$app->response->statusCode = Status::STATUS_BAD_REQUEST;
            $model->getErrors();
            $response['hasErrors'] = $model->hasErrors();
            $response['errors'] = $model->getErrors();
            return [
                'status' => Status::STATUS_BAD_REQUEST,
                'message' => 'Error saving data!',
                'data' => [
                    'hasErrors' => $model->hasErrors(),
                    'getErrors' => $model->getErrors(),
                ]
            ];
        }
    }

    public function actionLogin()
    {
        $params = Yii::$app->request->post();
        if(empty($params['username']) || empty($params['password'])) return [
            'status' => Status::STATUS_BAD_REQUEST,
            'message' => "Need username and password.",
            'data' => ''
        ];
        $user = User::findByUsername($params['username']);
        
       
        if (User::findByUsername($params['username'])&&$user->validatePassword($params['password'])) {


            Yii::$app->response->statusCode = Status::STATUS_OK;
            $user->generateAuthKey();
            $user->save();
            $expire_token = AccessToken::find()->where(['token'=>$user->pgw_auth_key])->one();
            return [
                'status' => Status::STATUS_OK,
                'message' => 'Login Succeed, save your token',
                'data' => [
                    'username' => $user->pgw_username,
                    'token' => $user->pgw_auth_key,
                    'email' => $user['pgw_email'],
                    'expire_token'=>$expire_token->expire_at,
                ]
            ];
        } else {
            Yii::$app->response->statusCode = Status::STATUS_UNAUTHORIZED;
            return [
                'status' => Status::STATUS_UNAUTHORIZED,
                'message' => 'Username and Password not found. Atau Tidak punya akses untuk aplikasi ini!',
                'data' => ''
            ];
        }
    }
    
    public function actionCheckToken()
    {
        $params = Yii::$app->request->post();
        if(empty($params['token'])) return [
            'status' => Status::STATUS_BAD_REQUEST,
            'message' => "Need username and password.",
            'data' => ''
        ];
        $user = UserIdentity::findIdentityByAccessToken($params['token']);
        
       
        if (UserIdentity::findIdentityByAccessToken($params['token'])) {


            Yii::$app->response->statusCode = Status::STATUS_OK;
            // $user->generateAuthKey();
            // $user->save();
            return [
                'status' => Status::STATUS_OK,
                'message' => 'Login Succeed, save your token',
                // 'data' => [
                //     'username' => $user->pgw_username,
                //     'token' => $user->pgw_auth_key,
                //     'email' => $user['pgw_email'],
                // ]
            ];
        } else {
            Yii::$app->response->statusCode = Status::STATUS_UNAUTHORIZED;
            return [
                'status' => Status::STATUS_UNAUTHORIZED,
                'message' => 'Token Tidak Valid',
                'data' => ''
            ];
        }
    }
}