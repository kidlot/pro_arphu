<?php
namespace Think\Session\Driver;

class Redis {
	protected $lifeTime     = 1200; // 单位：秒
	protected $sessionName  = '';
	protected $handle       = null;

    /**
     * 打开Session 
     * @access public 
     * @param string $savePath 
     * @param mixed $sessName  
     */
	public function open($savePath, $sessName) {
		$this->lifeTime     = C('SESSION_OPTIONS.expire') ? C('SESSION_OPTIONS.expire') : $this->lifeTime;
		
		$this->handle       = new \Redis;
        $host               = C('REDIS_HOST') ? : '127.0.0.1';
        $port               = C('REDIS_PORT') ? : '6379';
       	
        $this->handle->connect($host, $port);
        empty(C('REDIS_DB'))?:$this->handle->select(C('REDIS_DB'));
		return true;
	}

    /**
     * 关闭Session 
     * @access public 
     */
	public function close() {
		$this->gc(ini_get('session.gc_maxlifetime'));
		$this->handle->close();
		$this->handle       = null;
		return true;
	}

    /**
     * 读取Session 
     * @access public 
     * @param string $sessID 
     */
	public function read($sessID) {
		if(is_int($this->lifeTime) && $this->lifeTime) {
			$this->handle->expire($this->sessionName.$sessID, $this->lifeTime);
		}
        return $this->handle->get($this->sessionName.$sessID);
	}

    /**
     * 写入Session 
     * @access public 
     * @param string $sessID 
     * @param String $sessData  
     */
	public function write($sessID, $sessData) {
		if(is_int($this->lifeTime) && $this->lifeTime) {
			$this->handle->set($this->sessionName.$sessID, $sessData, $this->lifeTime);
		}else{
			$this->handle->set($this->sessionName.$sessID, $sessData);
		}
	}

    /**
     * 删除Session 
     * @access public 
     * @param string $sessID 
     */
	public function destroy($sessID) {
		return $this->handle->delete($this->sessionName.$sessID);
	}

    /**
     * Session 垃圾回收
     * @access public 
     * @param string $sessMaxLifeTime 
     */
	public function gc($sessMaxLifeTime) {
		return true;
	}
}
