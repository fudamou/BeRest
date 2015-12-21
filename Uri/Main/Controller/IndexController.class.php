<?php
namespace Main\Controller;

use Think\Controller;

class IndexController extends Controller
{
    public function index()
    {
        //echo get_random_str(8,'NUMBER');
        echo token_encode('8266679','123456','');
       //$a = token_decode('rMr0KLxwtEoaQjqailwJqycKvx42SnfhlVkwoADuGKpw52c9YkQ+U20clZ/VswjgOg9zsPSN2VHcFETFeoSfpjcVrQlfhzEhdZTSGzq0MChnEt6b4qzjapH4npbn+UgBbWtBUAF88jKUiVjwdj6tFkeOtzkOWMprMk7SkvYUp3E=');
    }
}

/*
 * rMr0KLxwtEoaQjqailwJqycKvx42SnfhlVkwoADuGKpw52c9YkQ+U20clZ/VswjgOg9zsPSN2VHcFETFeoSfpjcVrQlfhzEhdZTSGzq0MChnEt6b4qzjapH4npbn+UgBbWtBUAF88jKUiVjwdj6tFkeOtzkOWMprMk7SkvYUp3E=
 */