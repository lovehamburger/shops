<?php
/**
 * 父类事件
 * @author YangXB 2016.7.11
 * @copyright 晨丰科技有限公司
 * @abstract 父类事件
 */
namespace Admin\Event;

class BaseEvent{
    protected $__SUCCESS = array();
    protected $__DBERROR = array();

    public function __construct()
    {
        $this->__SUCCESS = array_err(0, 'success');
        $this->__DBERROR = array_err(41000, '数据库异常');
    }
}