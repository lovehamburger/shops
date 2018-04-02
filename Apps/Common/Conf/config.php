<?php
return array(
	'SHOW_PAGE_TRACE'=>true,
	//应用设定
	'APP_USE_NAMESPACE'     =>  true,    // 应用类库是否使用命名空间 3.2.1新增
	'APP_SUB_DOMAIN_DEPLOY' =>  false,   // 是否开启子域名部署
	'APP_SUB_DOMAIN_RULES'  =>  array(), // 子域名部署规则
	'APP_DOMAIN_SUFFIX'     =>  '', // 域名后缀 如果是com.cn net.cn 之类的后缀必须设置    
	'ACTION_SUFFIX'         =>  '', // 操作方法后缀
	'MULTI_MODULE'          =>  true, // 是否允许多模块 如果为false 则必须设置 DEFAULT_MODULE
	'MODULE_DENY_LIST'      =>  array('Common','Runtime'), // 禁止访问的模块列表
	'MODULE_ALLOW_LIST' 	=> array('Home','Admin','User'),
	'CONTROLLER_LEVEL'      =>  1,
	'APP_AUTOLOAD_LAYER'    =>  'Controller,Model', // 自动加载的应用类库层（针对非命名空间定义类库） 3.2.1新增
	'APP_AUTOLOAD_PATH'     =>  '', // 自动加载的路径（针对非命名空间定义类库） 3.2.1新增

	//默认设定
	'DEFAULT_M_LAYER'       =>  'Model', // 默认的模型层名称
	'DEFAULT_C_LAYER'       =>  'Controller', // 默认的控制器层名称
	'DEFAULT_V_LAYER'       =>  'View', // 默认的视图层名称
	'DEFAULT_LANG'          =>  'zh-cn', // 默认语言
	'DEFAULT_THEME'         =>  '', // 默认模板主题名称
	'DEFAULT_MODULE'        =>  'Home',  // 默认模块
	'DEFAULT_CONTROLLER'    =>  'Index', // 默认控制器名称
	'DEFAULT_ACTION'        =>  'index', // 默认操作名称
	'DEFAULT_CHARSET'       =>  'utf-8', // 默认输出编码
	'DEFAULT_TIMEZONE'      =>  'PRC',  // 默认时区
	'DEFAULT_AJAX_RETURN'   =>  'JSON',  // 默认AJAX 数据返回格式,可选JSON XML ...
	'DEFAULT_JSONP_HANDLER' =>  'jsonpReturn', // 默认JSONP格式返回的处理方法
	'DEFAULT_FILTER'        =>  'htmlspecialchars', // 默认参数过滤方法 用于I函数...

	//Cookie设置
	'COOKIE_EXPIRE'         =>  0,    // Cookie有效期
	'COOKIE_DOMAIN'         =>  '',      // Cookie有效域名
	'COOKIE_PATH'           =>  '/',     // Cookie路径
	'COOKIE_PREFIX'         =>  '',      // Cookie前缀 避免冲突
	'COOKIE_HTTPONLY'       =>  '',     // Cookie的httponly属性 3.2.2新增

	//数据库设置
    'DB_TYPE'=>'pdo',
    //数据库类型
    'DB_USER'=>'root',
    //用户名
    'DB_PWD'=>'root',
    //密码
    'DB_PREFIX'=>'tp_',
    //数据库表前缀
    'DB_DSN'=>'mysql:host=localhost;dbname=tpshop;charset=UTF8',

    //'配置项'=>'配置值'
    'LOAD_EXT_FILE'         => 'functions,head,sendmsg,encrypt,redis,arraydeal,business,map,apiCall,files',
    'LOAD_EXT_CONFIG'       => 'weixin',
    'DATA_AUTH'             => true, //是否加密
    'DATA_AUTH_KEY'         => 'CLH', //系统密钥
    'LAST_URL_NAME'         => 'PLU', //上次访问地址的cookie键值
    'LAST_POST_NAME'        => 'PLP', //上次post参数的cookie键值

    'TMPL_ACTION_ERROR'     =>  THINK_PATH.'Tpl/err_sucess.html', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS'   =>  THINK_PATH.'Tpl/err_sucess.html', // 默认成功跳转对应的模板文件

    /* 数据缓存设置 */
    'DATA_CACHE_TIME'       =>  0,      // 数据缓存有效期 0表示永久缓存
    'DATA_CACHE_COMPRESS'   =>  false,   // 数据缓存是否压缩缓存
    'DATA_CACHE_CHECK'      =>  false,   // 数据缓存是否校验缓存
    'DATA_CACHE_PREFIX'     =>  '',     // 缓存前缀
    'DATA_CACHE_TYPE'       =>  'Redis',  // 数据缓存类型,支持:File|Db|Apc|Memcache|Shmop|Sqlite|Xcache|Apachenote|Eaccelerator
    'DATA_CACHE_PATH'       =>  TEMP_PATH,// 缓存路径设置 (仅对File方式缓存有效)
    'DATA_CACHE_KEY'        =>  '',	// 缓存文件KEY (仅对File方式缓存有效)
    'DATA_CACHE_SUBDIR'     =>  false,    // 使用子目录缓存 (自动根据缓存标识的哈希创建子目录)
    'DATA_PATH_LEVEL'       =>  1,        // 子目录缓存级别
    'DATA_CACHE_TIMEOUT'    => '0',//子目录缓存级别
    'DB_FIELDS_CACHE'       =>  true,  // 启用字段缓存

	//错误设置
	'ERROR_MESSAGE'         =>  '页面错误！请稍后再试～',//错误显示信息,非调试模式有效
	'ERROR_PAGE'            =>  '', // 错误定向页面
	'SHOW_ERROR_MSG'        =>  false,    // 显示错误信息
	'TRACE_MAX_RECORD'      =>  100,    // 每个级别的错误信息 最大记录数

	//日志设置
	'LOG_RECORD'            =>  true,   // 默认不记录日志
	'LOG_TYPE'              =>  'File', // 日志记录类型 默认为文件方式
	'LOG_LEVEL'             =>  'EMERG,ALERT,CRIT,ERR',// 允许记录的日志级别
	'LOG_EXCEPTION_RECORD'  =>  false,    // 是否记录异常信息日志

	//SESSION设置
	'SESSION_AUTO_START'    =>  true,    // 是否自动开启Session
    'SESSION_OPTIONS'       =>  array(), // session 配置数组 支持type name id path expire domain 等参数
    'SESSION_TYPE'          =>  'Redis', // session hander类型 默认无需设置 除非扩展了session hander驱动 Redis
    'SESSION_PREFIX'        =>  'sess', // session 前缀 sess_
    'REDIS_HOST' => '127.0.0.1', //REDIS服务器地址 192.168.1.241
    'REDIS_PORT' => 6379, //REDIS连接端口号 22122
    'SESSION_EXPIRE' => 3600, //SESSION过期时间 3600
    'VAR_SESSION_ID'      =>  'PFWSSS',//sessionID的提交变量

	//模板引擎设置
	'TMPL_CONTENT_TYPE'     =>  'text/html', // 默认模板输出类型
	'TMPL_ACTION_ERROR'     =>  THINK_PATH.'Tpl/dispatch_jump.tpl', // 默认错误跳转对应的模板文件
	'TMPL_ACTION_SUCCESS'   =>  THINK_PATH.'Tpl/dispatch_jump.tpl', // 默认成功跳转对应的模板文件
	'TMPL_EXCEPTION_FILE'   =>  THINK_PATH.'Tpl/think_exception.tpl',// 异常页面的模板文件
	'TMPL_DETECT_THEME'     =>  false,       // 自动侦测模板主题
	'TMPL_TEMPLATE_SUFFIX'  =>  '.html',     // 默认模板文件后缀
	'TMPL_FILE_DEPR'        =>  '/', //模板文件CONTROLLER_NAME与ACTION_NAME之间的分割符
	'TMPL_ENGINE_TYPE'      =>  'Think',     // 默认模板引擎 以下设置仅对使用Think模板引擎有效
	'TMPL_CACHFILE_SUFFIX'  =>  '.php',      // 默认模板缓存后缀
	'TMPL_DENY_FUNC_LIST'   =>  'echo,exit',    // 模板引擎禁用函数
	'TMPL_DENY_PHP'         =>  false, // 默认模板引擎是否禁用PHP原生代码
	'TMPL_L_DELIM'          =>  '{',            // 模板引擎普通标签开始标记
	'TMPL_R_DELIM'          =>  '}',            // 模板引擎普通标签结束标记
	'TMPL_VAR_IDENTIFY'     =>  'array',     // 模板变量识别。留空自动判断,参数为'obj'则表示对象
	'TMPL_STRIP_SPACE'      =>  true,       // 是否去除模板文件里面的html空格与换行
	'TMPL_CACHE_ON'         =>  true,        // 是否开启模板编译缓存,设为false则每次都会重新编译
	'TMPL_CACHE_PREFIX'     =>  '',         // 模板缓存前缀标识，可以动态改变
	'TMPL_CACHE_TIME'       =>  0,         // 模板缓存有效期 0 为永久，(以数字为值，单位:秒)
	'TMPL_LAYOUT_ITEM'      =>  '{__CONTENT__}', // 布局模板的内容替换标识
	'LAYOUT_ON'             =>  false, // 是否启用布局
	'LAYOUT_NAME'           =>  'layout', // 当前布局名称 默认为layout

	//URL设置
	'URL_CASE_INSENSITIVE'  =>  true,   // 默认false 表示URL区分大小写 true则表示不区分大小写
	'URL_MODEL'             =>  2,       // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
	// 0 (普通模式); 1 (PATHINFO 模式); 2 (REWRITE  模式); 3 (兼容模式)  默认为PATHINFO 模式
	'URL_PATHINFO_DEPR'     =>  '-',    // PATHINFO模式下，各参数之间的分割符号
	'URL_PATHINFO_FETCH'    =>  'ORIG_PATH_INFO,REDIRECT_PATH_INFO,REDIRECT_URL', // 用于兼容判断PATH_INFO 参数的SERVER替代变量列表
	'URL_REQUEST_URI'       =>  'REQUEST_URI', // 获取当前页面地址的系统变量 默认为REQUEST_URI
	'URL_HTML_SUFFIX'       =>  'html',  // URL伪静态后缀设置
	'URL_DENY_SUFFIX'       =>  'ico|png|gif|jpg', // URL禁止访问的后缀设置
	'URL_PARAMS_BIND'       =>  true, // URL变量绑定到Action方法参数
	'URL_PARAMS_BIND_TYPE'  =>  0, // URL变量绑定的类型 0 按变量名绑定 1 按变量顺序绑定
	'URL_404_REDIRECT'      =>  '', // 404 跳转页面 部署模式有效
	'URL_ROUTER_ON'         =>  false,   // 是否开启URL路由
	'URL_ROUTE_RULES'       =>  array(), // 默认路由规则 针对模块
	'URL_MAP_RULES'         =>  array(), // URL映射定义规则

	//系统变量名称设置
	'VAR_MODULE'            =>  'm',     // 默认模块获取变量
	'VAR_CONTROLLER'        =>  'c',    // 默认控制器获取变量
	'VAR_ACTION'            =>  'a',    // 默认操作获取变量
	'VAR_AJAX_SUBMIT'       =>  'ajax',  // 默认的AJAX提交变量
	'VAR_JSONP_HANDLER'     =>  'callback',
	'VAR_PATHINFO'          =>  's',    // 兼容模式PATHINFO获取变量例如 ?s=/module/action/id/1 后面的参数取决于URL_PATHINFO_DEPR
	'VAR_TEMPLATE'          =>  't',    // 默认模板切换变量
	'VAR_ADDON'             =>  'addon',    // 默认的插件控制器命名空间变量 3.2.2新增

	//其他设置
	'HTTP_CACHE_CONTROL'    =>  'private',  // 网页缓存控制
	'CHECK_APP_DIR'         =>  true,       // 是否检查应用目录是否创建
	'FILE_UPLOAD_TYPE'      =>  'Local',    // 文件上传方式
	'DATA_CRYPT_TYPE'       =>  'Think',    // 数据加密方式

    //短信信息
    'THINK_SMS' => array(
        'APPKEY' => 'LTAI7mgNp89saH7g',
        'APPSECRET' => 'ejwDNmgosfBgU0PvVcqFPD9D0hpCO6',
        'APPSIGNNAME' => '好加快', //短信签名
    ),

	'ADMIN_UPLOAD_BRAND'=> array(// 上传品牌图片
        'maxSize'    =>    3145728,
        'rootPath'   =>    './Uploads/',
        'savePath'   =>    'brand/',
        'saveName'   =>    array('uniqid', array('', true)),
        'exts'       =>    array('jpg', 'png', 'jpeg'),
        'replace'    =>    true,
        'autoSub'    =>    false,
    ),
    'UPLOAD_GOODS'=> array(// 上传商品主图图片
        'maxSize'    =>    3145728,
        'rootPath'   =>    './Uploads/',
        'savePath'   =>    'goods/',
        'subName'    =>    array('date','Ymd'),
        'saveName'   =>    array('uniqid', array('', true)),
        'exts'       =>    array('jpg', 'png', 'jpeg'),
        'replace'    =>    true,
        'autoSub'    =>    true,
    ),
    //品牌图片LOGO
	'GOOD_BREAD' =>array(
		'WIDTH' => 120,
		'HEIGHT' => 50,
	),
	//商品图片LOGO
	'GOODS_IMG' =>array(
		'max_thumb' => array(
		'WIDTH' => 362,
		'HEIGHT' => 362
		),
		'mid_thumb' => array(
		'WIDTH' => 222,
		'HEIGHT' => 222
		),
		'sm_thumb' => array(
		'WIDTH' => 67,
		'HEIGHT' => 67
		)
	),

	//商品图片介绍
	'GOODS_IMG_DETAIL' =>array(
		'max_thumb'=>
			array(
			'WIDTH' => 362,
			'HEIGHT' => 362
			),
		'sm_thumb'=>
			array(
			'WIDTH' => 222,
			'HEIGHT' => 222
			)
		),
);