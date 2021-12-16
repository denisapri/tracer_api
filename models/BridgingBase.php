<?php
namespace app\models;
use Yii;
use linslin\yii2\curl\Curl;
class BridgingBase
{
    public $ver;

    public $baseUrl;
    public $finalUrl;

    public $cID;
    public $cSecret;

    public $signature;
    public $timestamp;

    public $method;
    public $setup;

    protected $parameter;

    public $header;

    public $errorMessage;
    public $errorCode;

    public $response;

    public function __construct()
    {
        date_default_timezone_set('UTC');
        $this->ver = \Yii::$app->params['bpjs']['bridging']['ver'];

        $this->baseUrl = \Yii::$app->params['bpjs']['bridging']['url'];
        $this->cID = \Yii::$app->params['bpjs']['bridging']['id'];
        $this->cSecret = \Yii::$app->params['bpjs']['bridging']['password'];

        $this->timestamp = strval(time()-strtotime('1970-01-01 00:00:00'));
        $this->signature = base64_encode(hash_hmac('sha256', $this->cID."&".$this->timestamp, $this->cSecret, true));
        $this->header = [
            "Content-Type" =>'Application/x-www-form-urlencoded',//'application/json; charset=utf-8',//Application/x-www-form-urlencoded',
            "X-cons-id" => $this->cID,
            "X-timestamp" => $this->timestamp,
            "X-signature" => $this->signature,
        ];
        $this->setup=[
            CURLOPT_SSL_VERIFYPEER=>false,
            CURLOPT_SSL_VERIFYHOST=>false,
            CURLOPT_SSL_CIPHER_LIST=>'DEFAULT:!DH'
        ];
        date_default_timezone_set('Asia/Jakarta');
    }

    /**
     * Setting URL API
     *
     * Ex: setUpUrl(['poli','ref','poli'])
     * Ex: setUpUrl('poli/ref/poli)
     *
     * @param Mixed_ $module
     * @param array $param
     */
    public function setUpUrl($module, $param = array(), $method = 'GET')
    {
        if(is_array($module)){
            $module = implode('/',$module);
        }
        $this->finalUrl = str_replace(' ','%20',$this->baseUrl.DIRECTORY_SEPARATOR.$module);
        // echo $this->finalUrl;
        $this->parameter = $param;
        $this->method = $method;
    }

    /**
     * Eksekusi perintah dengan nilai return data
     *
     * @return Mixed_
     */
    public function exec()
    {
        if(empty($this->finalUrl)){
            $this->createUrl();
        }
        $this->response = json_decode($this->execute());
        return $this->validate();
    }

    protected function execute()
    {
        $curl = new Curl();
        $curl->setHeaders($this->header);
        $curl->setOptions($this->setup);
        
        if(in_array($this->method,['POST','PUT','DELETE'])){
            $curl->setRequestBody($this->parameter);
        }else if( $this->method == 'GET'){
            $curl->setGetParams($this->parameter);
        }else{
            throw new InvalidParamException('Invalid Method Value on Class'.self::className());
        }

        if($this->method == 'POST'){
            $response = $curl->post($this->finalUrl);
        }else if( $this->method == 'GET'){
            $response = $curl->get($this->finalUrl);
        }else if($this->method == 'PUT'){
            $response = $curl->put($this->finalUrl);
        }else if($this->method == 'DELETE'){
            $response = $curl->delete($this->finalUrl);
        }

        return $response;
    }

    protected function validate()
    {
        if(!isset($this->response->metaData)){
            $this->errorMessage = 'Tidak dapat menghubungkan dengan server BPJS';
            $this->errorCode = 500;
            return false;
        }else{
            if($this->response->metaData->code == 200){
                $this->errorCode = null;
                $this->errorMessage = null;
                return true;
            }else{
                $this->errorCode = $this->response->metaData->code;
                $this->errorMessage = $this->response->metaData->message;
                return false;
            }
        }
    }

    public function getResponse()
    {
        if(!empty($this->response)){
            if(!empty($this->response->response)){
                if(empty($this->errorCode)){
                    return $this->response->response;
                }
            }
        }
        return null;
    }

    public function getParam()
    {
        return $this->parameter;
    }
}