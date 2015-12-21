<?php

    function get_http_header(){
        $headers = array();
        foreach ($_SERVER as $key => $value) {
            if ('HTTP_' == substr($key, 0, 5)) {
                $headers[str_replace('_', '-', substr($key, 5))] = $value;
            }
        }
        $header['DEVICE'] = $_SERVER['DEVICEID'];
        if (isset($_SERVER['PHP_AUTH_DIGEST'])) {
            $header['AUTHORIZATION'] = $_SERVER['PHP_AUTH_DIGEST'];
        } elseif (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
            $header['AUTHORIZATION'] = base64_encode($_SERVER['PHP_AUTH_USER'] . ':' . $_SERVER['PHP_AUTH_PW']);
        }
        if (isset($_SERVER['CONTENT_LENGTH'])) {
            $header['CONTENT-LENGTH'] = $_SERVER['CONTENT_LENGTH'];
        }
        if (isset($_SERVER['CONTENT_TYPE'])) {
            $header['CONTENT-TYPE'] = $_SERVER['CONTENT_TYPE'];
        }
        return $headers;
    }

    function get_rcode($code){
        static $_rcode = array(
            "0000"  =>  "执行成功",
            "0001"  =>  "邮箱或手机号码已存在",
            "0002"  =>  "注册名不存在",
            "0003"  =>  "密码错误",
            "0004"  =>  "非法调用",
            "0005"  =>  "系统错误",
            "0006"  =>  "登陆证书失效，请重新登陆"
        );

        if(array_key_exists($code,$_rcode)){
            return $_rcode[$code];
        }else{
            return "无状态码描述";
        }
    }

    function get_root_key(){
        $private_key = '-----BEGIN RSA PRIVATE KEY-----
MIICXQIBAAKBgQC3//sR2tXw0wrC2DySx8vNGlqt3Y7ldU9+LBLI6e1KS5lfc5jl
TGF7KBTSkCHBM3ouEHWqp1ZJ85iJe59aF5gIB2klBd6h4wrbbHA2XE1sq21ykja/
Gqx7/IRia3zQfxGv/qEkyGOx+XALVoOlZqDwh76o2n1vP1D+tD3amHsK7QIDAQAB
AoGBAKH14bMitESqD4PYwODWmy7rrrvyFPEnJJTECLjvKB7IkrVxVDkp1XiJnGKH
2h5syHQ5qslPSGYJ1M/XkDnGINwaLVHVD3BoKKgKg1bZn7ao5pXT+herqxaVwWs6
ga63yVSIC8jcODxiuvxJnUMQRLaqoF6aUb/2VWc2T5MDmxLhAkEA3pwGpvXgLiWL
3h7QLYZLrLrbFRuRN4CYl4UYaAKokkAvZly04Glle8ycgOc2DzL4eiL4l/+x/gaq
deJU/cHLRQJBANOZY0mEoVkwhU4bScSdnfM6usQowYBEwHYYh/OTv1a3SqcCE1f+
qbAclCqeNiHajCcDmgYJ53LfIgyv0wCS54kCQAXaPkaHclRkQlAdqUV5IWYyJ25f
oiq+Y8SgCCs73qixrU1YpJy9yKA/meG9smsl4Oh9IOIGI+zUygh9YdSmEq0CQQC2
4G3IP2G3lNDRdZIm5NZ7PfnmyRabxk/UgVUWdk47IwTZHFkdhxKfC8QepUhBsAHL
QjifGXY4eJKUBm3FpDGJAkAFwUxYssiJjvrHwnHFbg0rFkvvY63OSmnRxiL4X6EY
yI9lblCsyfpl25l7l5zmJrAHn45zAiOoBrWqpM5edu7c
-----END RSA PRIVATE KEY-----';
        $pi_key =  openssl_pkey_get_private($private_key);//这个函数可用来判断私钥是否是可用的，可用返回资源id Resource id
        return $pi_key;
    }

    function get_pub_key(){
        $public_key = '-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC3//sR2tXw0wrC2DySx8vNGlqt
3Y7ldU9+LBLI6e1KS5lfc5jlTGF7KBTSkCHBM3ouEHWqp1ZJ85iJe59aF5gIB2kl
Bd6h4wrbbHA2XE1sq21ykja/Gqx7/IRia3zQfxGv/qEkyGOx+XALVoOlZqDwh76o
2n1vP1D+tD3amHsK7QIDAQAB
-----END PUBLIC KEY-----';
        $pu_key = openssl_pkey_get_public($public_key);//这个函数可用来判断公钥是否是可用的
        return $pu_key;
    }

    /*私钥加密*/
    function private_encrypt($data){
        $encrypted = "";
        openssl_private_encrypt($data,$encrypted,get_root_key());//私钥加密
        $encrypted = base64_encode($encrypted);//加密后的内容通常含有特殊字符，需要编码转换下，在网络间通过url传输时要注意base64编码是否是url安全的
        return $encrypted;
    }

    /*公钥解密*/
    function public_decrypt($data){
        $decrypted ="";
        openssl_public_decrypt(base64_decode($data),$decrypted,get_pub_key());
        return $decrypted;
    }

    function public_encrypt($data){
        $encrypted="";
        openssl_public_encrypt($data,$encrypted,get_pub_key());//公钥加密
        $encrypted = base64_encode($encrypted);
        return $encrypted;
    }

    function private_decrypt($data){
        $decrypted="";
        openssl_private_decrypt(base64_decode($data),$decrypted,get_root_key());//私钥解密
        return $decrypted;
    }

    function token_encode($uid, $passwd, $device=""){
        return private_encrypt(json_encode
        (['uid'=>$uid,
                'passwd'=>$passwd,
                'device'=>$device,
                'ts'=>time()])
        );
    }

    function token_decode($token){
        return json_decode(public_decrypt($token));
    }

    function get_random_str($len=8,$format='ALL'){
        $is_abc = $is_numer = 0;
        $password = $tmp ='';
        switch($format){
            case 'ALL':
                $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                break;
            case 'CHAR':
                $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
                break;
            case 'NUMBER':
                $chars='0123456789';
                break;
            default :
                $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                break;
        }

        mt_srand((double)microtime()*1000000*getmypid());
        while(strlen($password)<$len){
            $tmp =substr($chars,(mt_rand()%strlen($chars)),1);
            if(($is_numer <> 1 && is_numeric($tmp) && $tmp > 0 )|| $format == 'CHAR'){
                $is_numer = 1;
            }
            if(($is_abc <> 1 && preg_match('/[a-zA-Z]/',$tmp)) || $format == 'NUMBER'){
                $is_abc = 1;
            }
            $password.= $tmp;
        }
        if($is_numer <> 1 || $is_abc <> 1 || empty($password) ){
            $password = get_random_str($len,$format);
        }
        return $password;
    }
?>