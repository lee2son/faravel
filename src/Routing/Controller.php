<?php

namespace Faravel\Routing;

class Controller extends \Illuminate\Routing\Controller
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
     * @throws
     */
    final public function callAction($method, $parameters)
    {
        $this->before();
        $result = call_user_func_array([$this, $method], $parameters);
        $response = $this->response($result);
        $this->after($response);
        return $response;
    }
}