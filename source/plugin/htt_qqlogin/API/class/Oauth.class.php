<?php
/* PHP SDK
 * @version 2.0.0
 * @author connect@qq.com
 * @copyright ? 2013, Tencent Corporation. All rights reserved.
 */

require_once(CLASS_PATH."Recorder.class.php");
require_once(CLASS_PATH."URL.class.php");
require_once(CLASS_PATH."ErrorCase.class.php");

class Oauth{

    const VERSION = "2.0";
    const GET_AUTH_CODE_URL = "https://graph.qq.com/oauth2.0/authorize";
    const GET_ACCESS_TOKEN_URL = "https://graph.qq.com/oauth2.0/token";
    const GET_OPENID_URL = "https://graph.qq.com/oauth2.0/me";

    protected $recorder;
    public $urlUtils;
    protected $error;

    public $appid;
    public $appkey;
    public $callback;


    function __construct(){
        $this->recorder = new Recorder();
        $this->urlUtils = new URL();
        $this->error = new ErrorCase();

        //��session��ȡ������
        $htt_qq_info = $_SESSION['htt_qq_info'];
        if(!empty($htt_qq_info)){
            $this->appid = $htt_qq_info['appid'];
            $this->appkey =  $htt_qq_info['appkey'];
            $this->callback =  $htt_qq_info['callback'];
        }


    }

    public function set_config($appid,$appkey,$callback){
        $this->appid = $appid;
        $this->appkey = $appkey;
        $this->callback = $callback;
        $_SESSION['htt_qq_info'] = array(
            'appid'=>$appid,
            'appkey'=>$appkey,
            'callback'=>$callback,
        );


    }


    public function qq_login(){

        /*
                $appid = $this->recorder->readInc("appid");
                $callback = $this->recorder->readInc("callback");
                $scope = $this->recorder->readInc("scope");

        */
        $appid = $this->appid;
        $callback = $this->callback;
//        $scope ='get_user_info,add_share,list_album,add_album,upload_pic,add_topic,add_one_blog,add_weibo,check_page_fans,add_t,add_pic_t,del_t,get_repost_list,get_info,get_other_info,get_fanslist,get_idolist,add_idol,del_idol,get_tenpay_addr';
        $scope ='get_user_info';


        //-------����Ψһ�������CSRF����
        $state = md5(uniqid(rand(), TRUE));
        $this->recorder->write('state',$state);

        //-------������������б�
        $keysArr = array(
            "response_type" => "code",
            "client_id" => $appid,
            "redirect_uri" => urlencode($callback),
            "state" => $state,
            "scope" => $scope
        );

        $login_url =  $this->urlUtils->combineURL(self::GET_AUTH_CODE_URL, $keysArr);

//        echo $login_url;
//        exit();



        header("Location:$login_url");
    }

    public function qq_callback(){


//        print_r($_GET);
//        echo '------------';
//        echo 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
//        exit();



        $state = $this->recorder->read("state");

        //--------��֤state��ֹCSRF����
        if($_GET['state'] != $state){
            $this->error->showError("30001");
        }

        //-------��������б�
        $keysArr = array(
            "grant_type" => "authorization_code",
            "client_id" => $this->appid,
            "redirect_uri" => urlencode($this->callback),
            "client_secret" => $this->appkey,
            "code" => $_GET['code']
        );

        //------��������access_token��url
        $token_url = $this->urlUtils->combineURL(self::GET_ACCESS_TOKEN_URL, $keysArr);
        $response = $this->urlUtils->get_contents($token_url);

        if(strpos($response, "callback") !== false){

            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response  = substr($response, $lpos + 1, $rpos - $lpos -1);
            $msg = json_decode($response);

            if(isset($msg->error)){
                $this->error->showError($msg->error, $msg->error_description);
            }
        }

        $params = array();
        parse_str($response, $params);

        $this->recorder->write("access_token", $params["access_token"]);
        return $params["access_token"];

    }

    public function get_openid(){

        //-------��������б�
        $keysArr = array(
            "access_token" => $this->recorder->read("access_token")
        );

        $graph_url = $this->urlUtils->combineURL(self::GET_OPENID_URL, $keysArr);
        $response = $this->urlUtils->get_contents($graph_url);

        //--------�������Ƿ���
        if(strpos($response, "callback") !== false){

            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response = substr($response, $lpos + 1, $rpos - $lpos -1);
        }

        $user = json_decode($response);
        if(isset($user->error)){
            $this->error->showError($user->error, $user->error_description);
        }

        //------��¼openid
        $this->recorder->write("openid", $user->openid);
        return $user->openid;

    }
}