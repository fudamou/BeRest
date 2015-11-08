<?php
namespace Main\Controller;

use Thor\RestController;

class IndexController extends RestController
{
    public function index()
    {
        print_r(C('URI_NAME'));
    }
}