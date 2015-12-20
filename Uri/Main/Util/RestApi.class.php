<?php
namespace Thor;

use Think\Controller\RestController;

class RestApi extends RestController
{
    protected $ouid = null;

    public function __construct()
    {
        parent::__construct();
        //$this->_type
        $headers = get_http_header();
        $token = json_decode(private_decrypt($headers['AUTHORIZATION']));
        if($token['device']!=headers('DEVICE')){
            $this->response('Error','json',500);
        }

        $this->ouid = $token['uid'];
        if($token['pwd'] != M('Users')->field('passwd')->getByOuid($this->ouid)['passwd']){
            $this->response('Unauthorized','json',401);
        }
    }

    //Token struct is {uid:X,pwd:X,device:X,timestamp:X}
}
