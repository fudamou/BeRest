<?php
namespace Main\Controller;

use Think\Controller;

class IndexController extends Controller
{
    public function index()
    {
        echo C('URL_ROUTER');
    }
}