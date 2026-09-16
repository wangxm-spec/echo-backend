<?php

namespace app\index\controller;

class Index extends Base
{
    function index(){
        return view('index/index');
    }
}