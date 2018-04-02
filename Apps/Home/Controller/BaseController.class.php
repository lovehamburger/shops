<?php
namespace Home\Controller;
use Think\Controller;
class BaseController extends Controller {
 	public function __construct(){
	     parent::__construct();
	 }

	 public function _initialize(){
 	    $this->getConfig();//获取配置信息数据
	 }

    /**
     * 获取配置信息数据
     */
    public function getConfig(){
        if(empty(S('tp_config'))){
            $tpshop_config = M('config')->cache(tp_config)->select();
            if(!empty($tpshop_config)){
                foreach ($tpshop_config as $key => $value) {
                    $tConfig[$value['inc_type'].'_'.$value['name']] = $value['value'];
                }
            }
            S('tp_config',$tConfig);
        }
        $tConfig = S('tp_config');
        $this->assign('tpshop_config',$tConfig);
    }

    /**
     * 判断是否是ajax
     */
   	public function _inputAjax(){
   		if(!IS_AJAX){
   			$this->ajaxReturn(array_err(99999,'非法请求!'));
   		}
   	}
}