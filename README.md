# faravel
扩展 laravel 框架

## 功能点
### 扩展 predis

+ 增加自旋锁

        /**
         * 自旋锁，如果锁被占用则等待，直到解锁
         * @param string $key 锁的key
         * @param callable $handle 加锁后的处理函数
         * @param int $expire 锁过期时间，单位：秒，过期之后自动解锁
         * @param int $usleep 如果发现被锁，等待多少us后再次尝试加锁
         * @throws \Throwable
         * @return mixed
         */
        public function spinLock($key, callable $handle, $expire = 120, $usleep = 10e3)
         
    使用方法：
     
        app('redis')->spinLock('{userid}', function() {
            // coding ...
        });
    
+ 互斥锁

        /**
         * 互斥锁，如果锁被占用则直接返回
         * @param $key string 锁的key
         * @param callable $handle 如果锁未被占用则调用此方法
         * @param $expire int 锁过期时间，单位：秒，过期之后自动解锁
         * @throws \Throwable
         * @return array(locked:bool, return:mixed)
         */
        public function mutexLock($key, callable $handle, $expire = 120)
        
    使用方法：
    
        app('redis')->mutexLock('{userid}', function() {
            // coding ...
        });