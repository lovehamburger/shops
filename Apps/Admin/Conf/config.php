<?php
return array(
	//'配置项'=>'配置值'
	'TMPL_PARSE_STRING'  =>array(
		'__JS__'    => 'Public/'.MODULE_NAME.'/js', // 增加新的JS类库路径替换规则
		'__CSS__' 	=> 'Public/'.MODULE_NAME.'/css', // 增加新的上传路径替换规则
		'__IMG__'   => 'Public/'.MODULE_NAME.'/img', // 增加新的上传路径替换规则
		'__LAYJS__'   => 'Public/layer', // 增加新的上传路径替换规则
		'__LAYPAGE__'   => 'Public/laypage', // 增加新的上传路径替换规则
		'__UEDITOR__'   => 'Public/ueditor', // 增加新的上传路径替换规则
		'__UPLOADIFY__'   => 'Public/uploadify', // 增加新的上传路径替换规则
	),
	//设置商品类型属性
	'ATTR_TYPE' => array(
		0 => '唯一属性',
		1 => '单选属性'
	)
	
);