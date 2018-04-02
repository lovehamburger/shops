<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
	public function index(){
		//获取首页轮播的图片
		// $field = 'picurl,link';
		// $headerAds = D('Home/Ads')->getAds(1,C('ADS.INDEX_HEADER'),$field);
		// $this->assign('headerAds',$headerAds);
		// //获取菜单树形
		// $mCate = D('Admin/Cate');
		// $cateRes = $mCate->catesTree();

		// echo'<pre>'; 
		// print_r($cateRes); 
		// echo'</pre>'; 
		        
		$this->assign('cateRes',$cateRes);
		$this->display();
	}
}