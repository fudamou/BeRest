<?php

namespace Main\Controller;
import("@.Util.RestApi");
use Think\Controller\RestController;
use Thor\RestApi;

class UserController extends RestController
{
    public function loginByUname($uname, $pwd){
        echo "ok".$uname.$pwd;
    }

    public function loginByToken(){
        echo "ok";
    }

    public function register(){
        echo "register ok";
    }

    public function update(){

    }
}