<?php

namespace Main\Controller;
import("@.Util.RestApi");
use Thor\RestApi;

/****
 * Class UserController
 * @package Main\Controller
 */
class UserController extends RestApi
{
    private  $m = null;
    protected $_noAuthorization = ['login','register','check'];

    protected function _initialize(){
        $this->m = M("Users");
    }

    protected function isExist($uname){
        $r = $this->m->field('uid,email,mobile,nickname,passwd')
            ->where("email='%s' or mobile='%s'",base64_decode($uname),$uname)
            ->find();
        return $r;
    }

    /**
     * @Router User/login/:uname/:passwd
     * @Method GET
     * @param $uname
     * @param $passwd
     */
    public function login($uname, $passwd, $isInternal=false){
        $r = $this->isExist($isInternal?base64_encode($uname):$uname);
        if($r){
            if($r['passwd'] == $passwd){
                $token = token_encode($r['uid'], $r['passwd']);
                $this->restResp("0000",201,['uid'=>$r['uid'], 'token'=>$token]);
            }else{
                $this->restResp("0003",200);
            }
        }else{
            $this->restResp("0002",204);
        }
    }

    /**
     * @Router User/:uname
     * @Method GET
     * @param $uid
     */
    public function check($uname){
        if($r = $this->isExist($uname)){
            $this->restResp(
                "0000",
                200,
                ["nickname"=>$r['nickname'], "uid"=>$r['uid']]);
        }else{
            $this->restResp("0002",204);
        }
    }


    /**
     * @Router User
     * @Method POST
     */
    public function register(){

        if(empty(I("post.email"))){
            $r = $this->isExist(I("post.mobile"));
        }else{
            $r = $this->isExist(I("post.email"));
        }
        //print_r($_POST);
        if($r){
            $this->restResp("0001",400);
        }

        $nuid = get_random_str(8,'NUMBER');
        do{
            $r = $this->m->field('uid')->find($nuid);
        }while($r);

        $data = [
            'email'=>empty(I("post.email"))? NULL : I("post.email"),
            'mobile'=>empty(I("post.mobile"))? NULL : I("post.mobile"),
            'uid'=>$nuid,
            'passwd'=>I("post.passwd"),
        ];

        $r = $this->m->add($data);
        if($r){
            //uid 200
            //$this->restResp("0000",201,['uid'=>$r]);
            $this->login(empty($data['email'])?$data['mobile']:$data['email'],$data['passwd']);
        }else{
            $this->restResp("0005",400);
        }
    }


    /**
     * @Router /User/:uid
     * @Method PUT
     * @param $uid
     */
    public function update($uid){

    }

    /**
     * @Router /User/:uid
     * @Method DELETE
     * @param $uid
     */
    public function delete($uid){
       if($this->uid != $uid){
           $this->restResp("0004",400);
       }

        $r = $this->m->where(['uid'=>$uid])->save(['user_sts'=>'C']);
        if($r){
            $this->restResp("0000",200);
        }else{
            $this->restResp("0005",500);
        }
    }
}