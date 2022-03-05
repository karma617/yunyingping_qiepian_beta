<?php
namespace app\common\server;

use app\common\exception\BaseException;
/**
 * server层基类
 *
 * @author 617 <email：723875993@qq.com>
 */
class Service {
    protected static $initialized = [];
    protected $error;
    /**
     * 架构函数
     * @access public
     * @param  array|object $data 数据
     */
    public function __construct($data = []) {
        // 执行初始化操作
        $this->initialize();
    }
	

    /**
     *  初始化模型
     * @access protected
     * @return void
     */
    protected function initialize() {
        if (!isset(static::$initialized[static::class])) {
            static::$initialized[static::class] = true;
            static::init();
        }
    }

    /**
     * 初始化处理
     * @access protected
     * @return void
     */
    protected static function init() {
        
    }

    /**
     * 获取错误信息
     * @access public
     * @return mixed
     */
    public function getError() {
        return $this->error;
    }
}
