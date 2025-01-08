<?php
/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

use Webman\Route;

// 添加默认路由
Route::get('/', [app\controller\IndexController::class, 'index']);

// 修改 info 路由，使用 request()->get('url') 来获取完整URL
Route::get('/info', [app\controller\IndexController::class, 'info']);

// 添加缓存列表路由
Route::get('/cache/list', [app\controller\IndexController::class, 'cacheList']);

// 禁用默认路由
Route::disableDefaultRoute();
