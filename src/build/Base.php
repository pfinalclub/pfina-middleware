<?php
/**
 * Created by PhpStorm.
 * User: 南丞
 * Date: 2019/3/8
 * Time: 10:17
 *
 *
 *                      _ooOoo_
 *                     o8888888o
 *                     88" . "88
 *                     (| ^_^ |)
 *                     O\  =  /O
 *                  ____/`---'\____
 *                .'  \\|     |//  `.
 *               /  \\|||  :  |||//  \
 *              /  _||||| -:- |||||-  \
 *              |   | \\\  -  /// |   |
 *              | \_|  ''\---/''  |   |
 *              \  .-\__  `-`  ___/-. /
 *            ___`. .'  /--.--\  `. . ___
 *          ."" '<  `.___\_<|>_/___.'  >'"".
 *        | | :  `- \`.;`\ _ /`;.`/ - ` : | |
 *        \  \ `-.   \_ __\ /__ _/   .-` /  /
 *  ========`-.____`-.___\_____/___.-`____.-'========
 *                       `=---='
 *  ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
 *           佛祖保佑       永无BUG     永不修改
 *
 */

namespace pf\middleware\build;


use pf\config\Config;

class Base
{
    protected $params;

    public function web($name, $params = [])
    {
        $middleware = Config::get('middleware.web.' . $name) ?: [];
        if (!empty($middleware)) {
            $this->params = $params;
            return $this->exe($middleware);
        }
    }

    public function exe($middleware)
    {
        $middleware = array_unique($middleware);
        $dispatcher = array_reduce(array_reverse($middleware), $this->callback(), function () {

        });
        $dispatcher();
        return true;
    }

    protected function callback()
    {
        return function ($callback, $class) {
            return function () use ($callback, $class) {
                $content = call_user_func_array([new $class, 'run'], [$callback, $this->params]);
                if ($content) {
                    echo is_object($content) ? $content : '';
                    die;
                }
            };
        };
    }

    /**
     * 添加应用中间件
     * @param $name
     * @param $class
     * @return mixed
     */
    public function add($name, $class)
    {
        $middleware = Config::get('middleware.web.' . $name) ?: [];
        foreach ($class as $c) {
            array_push($middleware, $c);
        }
        return Config::set('middleware.web.' . $name, array_unique($middleware));
    }

    public function globals()
    {
        $middleware = array_unique(Config::get('middleware.global'));
        return $this->exe($middleware);
    }
}