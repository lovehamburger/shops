<?php
namespace Home\Controller;
use Think\Controller;
class TestController extends Controller {
    public function index(){
   echo  S('test111',wwww); 
   $res = M('User')->select();
echo D()->getLastSql();
   echo'<pre>'; 
   print_r($res); 
   echo'</pre>'; 
           
    session('name','clh');
               
    }
}