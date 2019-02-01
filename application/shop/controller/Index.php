<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2019/1/28
 * Time: 13:36
 */
namespace app\shop\controller;

use think\Controller;

class Index extends Controller
{

    public function index()
    {
        return $this->fetch();
    }



}