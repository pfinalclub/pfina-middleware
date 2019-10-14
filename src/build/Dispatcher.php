<?php
	/**
	 * ----------------------------------------
	 * | Created By pfinal-middleware                 |
	 * | User: pfinal <lampxiezi@163.com>     |
	 * | Date: 2019/10/14                      |
	 * | Time: 下午1:09                        |
	 * ----------------------------------------
	 * |    _____  ______ _             _     |
	 * |   |  __ \|  ____(_)           | |    |
	 * |   | |__) | |__   _ _ __   __ _| |    |
	 * |   |  ___/|  __| | | '_ \ / _` | |    |
	 * |   | |    | |    | | | | | (_| | |    |
	 * |   |_|    |_|    |_|_| |_|\__,_|_|    |
	 * ----------------------------------------
	 */
	
	namespace pf\middleware\build;
	
	
	use pf\response\Response;
	
	class Dispatcher
	{
		/**
		 * 执行中间件
		 *
		 * @param $middleware
		 */
		public function middleware($middleware)
		{
			$middleware = array_reverse($middleware);
			$dispatcher = array_reduce(
				$middleware,
				$this->getSlice(),
				function () {
				}
			);
			$dispatcher();
		}
		
		/**
		 * @return \Closure
		 */
		protected function getSlice()
		{
			return function ($next, $step) {
				return function () use ($next, $step) {
					if ($content = call_user_func_array([new $step, 'run'], [$next])) {
						die(is_string($content) ? Response::make($content) : $content);
					}
				};
			};
		}
	}