<?php

namespace Faravel\Illuminate\Routing;

abstract class Controller extends \Illuminate\Routing\Controller
{
    /**
     * 在调用 action 方法之前调用
     * @return void
     */
    protected function before()
    {

    }

    /**
     * 在调用了 action 方法，并调用了 self::response 方法之后调用
     * @param $response self::response 的返回值
     * @return void
     */
    protected function after($response)
    {

    }

    /**
     * @param $result 调用 action 方法后的返回值
     * @return mixed
     */
    protected function response($result)
    {
        return $result;
    }

    /**
     * Execute an action on the controller.
     * @param string $method
     * @param array $parameters
     * @return \Symfony\Component\HttpFoundation\Response
     */
    final public function callAction($method, $parameters)
    {
        $this->before();
        $result = $this->_callAction($method, $parameters);
        $response = $this->response($result);
        $this->after($response);
        return $response;
    }

    /**
     * Execute an action on the controller.
     * @param string $method
     * @param array $parameters
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function _callAction($method, $parameters)
    {
        return call_user_func_array([$this, $method], $parameters);
    }
}