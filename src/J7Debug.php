<?php
namespace j7tools;

class j7debug
{
    public static function debug($v,$k = null,$type='log')
    {
        return self::instance()->_debug($v,$k,$type);
    }
    public static function trace($v,$k = null)
    {
        return self::instance()->_debug($v,$k,'trace');
    }


    public static function instance()
    {
        return new j7debug();
    }

    protected $_debug_config='FirePHP,ChromePhp';
    protected $_isdebug=false;
    public function __construct()
    {
        if(defined('J7_DEBUG_CONFIG'))
            $this->_debug_config = J7_DEBUG_CONFIG;

        if(  strpos($this->_debug_config,'FirePHP')!==false )
        {
            $this->_isdebug = true;
            require_once __DIR__ . '/helper/FirePHP.class.php';
            $Options = array('BACKTRACE_LEVEL'=>2,'BACKTRACE_DEEP'=>4);
            j7debug\FirePHP::getInstance(true)->setEnabled(true);
            j7debug\FirePHP::getInstance(true)->setOption('BACKTRACE_LEVEL',$Options['BACKTRACE_LEVEL']);
            j7debug\FirePHP::getInstance(true)->setOption('BACKTRACE_DEEP',$Options['BACKTRACE_DEEP']);
        }
        if(  strpos($this->_debug_config,'ChromePhp')!==false )
        {
            $this->_isdebug = true;
            require_once __DIR__ . '/helper/ChromePhp.php';
        }
        if(  strpos($this->_debug_config,'var_dump')!==false )
        {
            $this->_isdebug = true;
        }
    }

    public function _debug($info,$key = 'Debug:',$showp='log')
    {
        if( $this->_isdebug )
        {
            $showp = $showp?:'trace';
            $showp = strtolower($showp); //默认应该是log

            if( !in_array($showp,array('dump','trace','log','info','error','warn')) ){ $showp='trace'; }

            if( isset($_SERVER['REQUEST_URI']) )
            {
                if(  strpos($this->_debug_config,'FirePHP')!==false )
                {
                    j7debug\FirePHP::getInstance(true)->$showp($info, $key);
                }
                if(  strpos($this->_debug_config,'ChromePhp')!==false )
                {
                    $showpchrome = $showp; //默认应该是log
                    if (!in_array($showpchrome, array('log', 'info', 'error', 'warn'))) {
                        $showpchrome = 'warn';
                    }
                    j7debug\ChromePhp::$showpchrome($key, $info, isset($Options['BACKTRACE_LEVEL']) ? ($Options['BACKTRACE_LEVEL'] - 1) : 0);
                }
            }
            if(  strpos($this->_debug_config,'var_dump')!==false )
            {
                echo $key.PHP_EOL;
                var_dump($info);
            }
        }
    }
}