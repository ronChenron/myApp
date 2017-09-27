<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/9/27
 * Time: 18:06
 */

/**
 * 获取当前控制器与方法
 *
 * @return array
 */
if(!function_exists('getCurrentAction')){
    function getCurrentAction()
    {
        $action = \Route::current()->getActionName();
        list($class, $method) = explode('@', $action);
        $class = substr(strrchr($class,'\\'),1);

        return ['controller' => $class, 'method' => $method];
    }
}


/**
 * 获取当前控制器名
 *
 * @return string
 */
if(!function_exists('getCurrentControllerName')){
    function getCurrentControllerName()
    {
        return lcfirst(substr(getCurrentAction()['controller'], 0, -10));
    }
}


/**
 * 获取当前方法名
 *
 * @return string
 */
if(!function_exists('getCurrentMethodName')){
    function getCurrentMethodName()
    {
        return getCurrentAction()['method'];
    }
}