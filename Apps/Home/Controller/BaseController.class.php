<?php
namespace Home\Controller;
use Think\Controller;
class BaseController extends Controller {
 	//public function __construct(){
	//     parent::__construct();
	// }

   	public function _inputAjax(){
   		if(!IS_AJAX){
   			$this->ajaxReturn(array_err(99999,'非法请求!'));
   		}
   	}
}