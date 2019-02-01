<?php
/**
 *
 * @copyright JungoPhpFramework 深圳俊网网络有限公司 http://www.junnet.net/
 * @author 吴跃忠 <357397264@qq.com>
 */

namespace app\sys\model;


class FileLock
{

    /**
     * 文件标识符
     * @var
     */
    private $fd;


    /**
     * 锁定的类型
     * @var string
     */
    private $type;


    /**
     * 锁定的键值
     * @var string
     */
    private $key;


    /**
     * @var string
     */
    private $lock_file;


    /**
     * FileLock constructor.
     * @param $type
     * @param $key
     */
    public function __construct($type, $key)
    {
        $this->type = $type;
        $this->key = $key;
        $lock_file = $this->file();
        $this->lock_file = $lock_file;
    }


    /**
     * 开启锁
     * @return bool
     */
    public function LOCK_EX()
    {
        $this->fd = fopen($this->lock_file, "w+");
        return flock($this->fd, LOCK_EX);
    }


    /**
     * 解锁
     * @return void
     */
    public function LOCK_UN()
    {
        flock($this->fd, LOCK_UN);
        fclose($this->fd);
    }


    /**
     * 用户锁
     * @return string
     */
    private function file()
    {
        $dir = ROOT_PATH . 'data/lock/'.$this->type;
        createDir($dir);
        $file = ROOT_PATH . 'data/lock/'.$this->type.'/' . $this->key . '.lock';
        if (!is_file($file)) {
            file_put_contents($file, $this->key);
            chmod($file, 0777);
        }
        return $file;
    }


    /**
     * 是否是锁定状态
     * @return bool
     */
    public function isLocked()
    {
        // 查看文件是否能写入
        if (is_writeable($this->lock_file)) {
            return false;
        } else {
            return true;
        }
    }

}