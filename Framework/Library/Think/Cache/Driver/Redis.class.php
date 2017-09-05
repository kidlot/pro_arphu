<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace Think\Cache\Driver;
use Think\Cache;

defined('THINK_PATH') or exit();

/**
 * Redis缓存驱动 
 * 要求安装phpredis扩展：https://github.com/nicolasff/phpredis
 */
class Redis extends Cache {

    /**
     * 架构函数
     * @param array $options 缓存参数
     * @access public
     */
    public function __construct($options = array()) {
        if (!extension_loaded('redis')) {
            E(L('_NOT_SUPPORT_') . ':redis');
        }
        $options = array_merge(array(
            'host' => C('REDIS_HOST') ? : '127.0.0.1',
            'port' => C('REDIS_PORT') ? : 6379,
            'timeout' => C('DATA_CACHE_TIMEOUT') ? : false,
            'persistent' => false,
            'db' => C('REDIS_DB')? : 0,
            ), $options);

        $this->options = $options;
        $this->options['expire'] = isset($options['expire']) ? $options['expire'] : C('DATA_CACHE_TIME');
        $this->options['prefix'] = isset($options['prefix']) ? $options['prefix'] : C('DATA_CACHE_PREFIX');
        $this->options['length'] = isset($options['length']) ? $options['length'] : 0;
        $func = $options['persistent'] ? 'pconnect' : 'connect';
        $this->handler = new \Redis;
        $options['timeout'] === false ?
                $this->handler->$func($options['host'], $options['port']) :
                $this->handler->$func($options['host'], $options['port'], $options['timeout']);
        empty($options['db'])?:$this->handler->select($options['db']);
    }

    /**
     * 读取缓存
     * @access public
     * @param string $name 缓存变量名
     * @return mixed
     */
    public function get($name) {
        N('cache_read', 1);
        $value = $this->handler->get($this->options['prefix'] . $name);
        $jsonData = json_decode($value, true);
        return ($jsonData === NULL) ? $value : $jsonData; //检测是否为JSON数据 true 返回JSON解析数组, false返回源数据
    }

    /**
     * 写入缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $value  存储数据
     * @param integer $expire  有效时间（秒）
     * @return boolean
     */
    public function set($name, $value, $expire = null) {
        N('cache_write', 1);
        if (is_null($expire)) {
            $expire = $this->options['expire'];
        }
        $name = $this->options['prefix'] . $name;
        //对数组/对象数据进行缓存处理，保证数据完整性
        $value = (is_object($value) || is_array($value)) ? json_encode($value) : $value;
        if (is_int($expire) && $expire) {
            $result = $this->handler->setex($name, $expire, $value);
        } else {
            $result = $this->handler->set($name, $value);
        }
        if ($result && $this->options['length'] > 0) {
            // 记录缓存队列
            $this->queue($name);
        }
        return $result;
    }

    /**
     * 删除缓存
     * @access public
     * @param string $name 缓存变量名
     * @return boolean
     */
    public function rm($name) {
        return $this->handler->delete($this->options['prefix'] . $name);
    }

    /**
     * 清除缓存
     * @access public
     * @return boolean
     */
    public function clear() {
        return $this->handler->flushDB();
    }


    /**
     * 事务开始
     * @access public
     * @return boolean
     */
    public function multi() {
         $this->handler->multi();
    }

    /**
     * 事务结束
     * @access public
     * @return boolean
     */
    public function exec() {
         return $this->handler->exec();
    }

    /**
     * 监测
     * @access public
     * @return boolean
     */
   public function watch($key) {
   		 $this->handler->watch($key);
   }

    /**
     * 防止同一用户多次请求
     * @access public
     * @param string $name 检测变量，用户唯一键
     * @param string $time 间隔时间
     * @return boolean
     */
    public function protect($name,$time=1){
		$this->handler->watch($name);
		$ret_get = $this->handler->get($name);
		if(isset($ret_get) &&  (time()-$ret_get)<$time){
			return false;
		}
		$this->handler->multi();
		$data = time();
		$ret_lock =$this->handler->set($name, $data)->exec();
		return $ret_lock;
    }

    /**
     * 监测
     * @access public
     * @param string $name
     * @return boolean
     */
    public function incr($name){
		$ret = $this->handler->incr($name);
		return $ret;
    }
	//检查/设置用户访问频率 指定 user_marked在 time_slot 秒内最多访问 count次；
	public function limiting($time_slot = 86400, $count = 1000, $cache_key = '') {
		$millisecond = intval(microtime(true)*1000 );
		$this->handler->pexpire($cache_key, $time_slot); // 设置过期时间
		$list_len = $this->handler->llen($cache_key);
		if( $list_len<$count ) {
			$this->handler->lpush($cache_key, $millisecond );
			return true;
		} else {
			$last_millisecond = $this->handler->lindex($cache_key, -1); // -1 标示列表最后一个元素
			if( ($millisecond-$last_millisecond)<$time_slot ) {
				$this->handler->ltrim($cache_key, -1, 0); //清空列表
				return false;
			} else {
				$this->handler->lpush($cache_key, $millisecond );
				$this->handler->ltrim($cache_key, 0, $count-1);
				return true;
			}
		}
	}

}
