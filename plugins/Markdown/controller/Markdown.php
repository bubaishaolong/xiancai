<?php
namespace plugins\Markdown\controller;

use app\common\controller\Common;
// use think\Exception;
// use think\Validate;
//加载markdown解释库
include config('plugin_path').'Markdown'.DS.'libs'.DS.'parsedown'.DS.'Parsedown.php';

use Parsedown;

class Markdown extends Common
{
    protected static $config; //配置

    protected function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
    }


    /**
     * 解释markdown文件成html
     * @param string $title     文件
     * @return string 返回html文件内容
     */
    public static function output($fileName = null)
    {
       $fileName = $fileName==null?config('plugin_path').'Markdown'.DS.'libs'.DS.'parsedown'.DS.'README.md':$fileName;
       if (file_exists($fileName)){
           $Parsedown = new Parsedown();
           $output = $Parsedown->text(file_get_contents($fileName));
       } else {
           $output = '';
       }
       return $output;
    }

}