<?php
namespace Thor;

use Think\Controller\RestController;

class RestApi extends RestController
{
    protected $uid = null;

    public function __construct()
    {
        parent::__construct();
        if (method_exists($this, '_initialize')) {
            $this->_initialize();
        }

        $needAuthorization = true;
        if(property_exists($this, '_noAuthorization')){
            $needAuthorization = in_array(ACTION_NAME, $this->_noAuthorization)?false:true;
        }

        if($needAuthorization){
            $headers = get_http_header();
            //var_dump(oauth_get_sbs());
            $token = token_decode($headers['AUTHORIZATION']);

            //设备认证
            /*if($token['device']!=headers('DEVICE')){
                $this->response('Error','json',500);
            }*/

            $this->uid = $token->uid;
            if($token->passwd != M('Users')->field('passwd')->find($this->uid)['passwd']){
                $this->restResp("0004",403);
            }

            //token超时时间60天
            if(abs($token->ts - time()) > 60*24*60*60){
                $this->restResp("0006",409);
            }
        }
    }

    /**
     *
     */
    protected function restResp($code,$status=200,$r=array()){
        parent::response(['code'=>$code, 'msg'=> get_rcode($code),'r'=>$r],'json',$status);
    }

    //Token struct is {uid:X,passwd:X,device:X,timestamp:X}
}
