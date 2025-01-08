<?php

namespace app\controller;

use support\Request;

class IndexController
{
    // 静态缓存数组
    private static $cache = [];
    // 缓存过期时间（秒）
    private static $cacheExpire = 3600; // 1小时
    // 每页显示数量
    private static $perPage = 10;

    public function index(Request $request)
    {
        return json([
            'code'=>200,
            'msg'=>'API is Running',
            'data'=>[
                'Client IP'=>$request->getRealIp(),
                'time'=>date('Y-m-d H:i:s'),
                'content'=>'This is a Web API for LUX, Only allowed Lux -j Command',
                'lux Version'=>'0.24.1',
                'Usage'=>'Visit url /info?url=Your need URL',
                'Cache Time'=>'3600s',
                'Cache Size'=>count(self::$cache),
                'Cache List'=>'url/cache/list?page=1&per_page=20',
                'support_list'=>[
                    ['Site' => '抖音', 'URL' => 'https://www.douyin.com'],
                    ['Site' => '哔哩哔哩', 'URL' => 'https://www.bilibili.com'],
                    ['Site' => '半次元', 'URL' => 'https://bcy.net'],
                    ['Site' => 'pixivision', 'URL' => 'https://www.pixivision.net'],
                    ['Site' => '优酷', 'URL' => 'https://www.youku.com'],
                    ['Site' => 'YouTube', 'URL' => 'https://www.youtube.com'],
                    ['Site' => '西瓜视频（头条）', 'URL' => 'https://m.toutiao.com, https://v.ixigua.com, https://www.ixigua.com'],
                    ['Site' => '爱奇艺', 'URL' => 'https://www.iqiyi.com'],
                    ['Site' => '新片场', 'URL' => 'https://www.xinpianchang.com'],
                    ['Site' => '芒果 TV', 'URL' => 'https://www.mgtv.com'],
                    ['Site' => '糖豆广场舞', 'URL' => 'https://www.tangdou.com'],
                    ['Site' => 'Tumblr', 'URL' => 'https://www.tumblr.com'],
                    ['Site' => 'Facebook', 'URL' => 'https://facebook.com'],
                    ['Site' => '斗鱼视频', 'URL' => 'https://v.douyu.com'],
                    ['Site' => '秒拍', 'URL' => 'https://www.miaopai.com'],
                    ['Site' => '微博', 'URL' => 'https://weibo.com'],
                    ['Site' => 'Instagram', 'URL' => 'https://www.instagram.com'],
                    ['Site' => 'Threads', 'URL' => 'https://www.threads.net'],
                    ['Site' => 'Twitter', 'URL' => 'https://twitter.com'],
                    ['Site' => '腾讯视频', 'URL' => 'https://v.qq.com'],
                    ['Site' => '网易云音乐', 'URL' => 'https://music.163.com'],
                    ['Site' => '音悦台', 'URL' => 'https://yinyuetai.com'],
                    ['Site' => '极客时间', 'URL' => 'https://time.geekbang.org'],
                    ['Site' => 'Pornhub', 'URL' => 'https://pornhub.com'],
                    ['Site' => 'XVIDEOS', 'URL' => 'https://xvideos.com'],
                    ['Site' => '聯合新聞網', 'URL' => 'https://udn.com'],
                    ['Site' => 'TikTok', 'URL' => 'https://www.tiktok.com'],
                    ['Site' => 'Pinterest', 'URL' => 'https://www.pinterest.com'],
                    ['Site' => '好看视频', 'URL' => 'https://haokan.baidu.com'],
                    ['Site' => 'AcFun', 'URL' => 'https://www.acfun.cn'],
                    ['Site' => 'Eporner', 'URL' => 'https://eporner.com'],
                    ['Site' => 'StreamTape', 'URL' => 'https://streamtape.com'],
                    ['Site' => '虎扑', 'URL' => 'https://hupu.com'],
                    ['Site' => '虎牙视频', 'URL' => 'https://v.huya.com'],
                    ['Site' => '喜马拉雅', 'URL' => 'https://www.ximalaya.com'],
                    ['Site' => '快手', 'URL' => 'https://www.kuaishou.com'],
                    ['Site' => 'Reddit', 'URL' => 'https://www.reddit.com'],
                    ['Site' => 'VKontakte', 'URL' => 'https://vk.com'],
                    ['Site' => '知乎', 'URL' => 'https://zhihu.com'],
                    ['Site' => 'Rumble', 'URL' => 'https://rumble.com'],
                    ['Site' => '小红书', 'URL' => 'https://xiaohongshu.com'],
                    ['Site' => 'Zing MP3', 'URL' => 'https://zingmp3.vn'],
                    ['Site' => 'Bitchute', 'URL' => 'https://www.bitchute.com'],
                    ['Site' => 'Odysee', 'URL' => 'https://odysee.com']
                ],
            ]
        ]);
    }

    public function info(Request $request)
    {
        $url = $request->get('url');
        
        if (empty($url)) {
            return json([
                'code' => 400,
                'msg' => '请提供视频URL',
                'data' => null
            ]);
        }

        try {
            // 检查缓存
            $cacheKey = md5($url);
            $now = time();
            
            if (isset(self::$cache[$cacheKey])) {
                $cached = self::$cache[$cacheKey];
                // 检查缓存是否过期
                if ($cached['expire'] > $now) {
                    // 返回缓存数据
                    return json([
                        'code' => 200,
                        'msg' => '解析成功(cached)',
                        'data' => $cached['data']
                    ]);
                }
                // 缓存过期，删除它
                unset(self::$cache[$cacheKey]);
            }

            // 使用完整的命令路径
            $luxPath = '/usr/bin/lux';
            
            // 检查命令是否存在且可执行
            if (!file_exists($luxPath) || !is_executable($luxPath)) {
                throw new \Exception("Lux命令不可用或无执行权限");
            }
            
            // 构建命令
            $command = $luxPath . ' -j ' . escapeshellarg($url);
            
            // 执行命令
            exec($command . ' 2>&1', $output, $return_value);
            
            // 将输出数组合并成字符串
            $outputStr = implode("\n", $output);
            
            // 如果命令执行失败
            if ($return_value !== 0) {
                throw new \Exception("命令执行失败\n命令: {$command}\n输出: {$outputStr}\n退出码: {$return_value}");
            }
            
            if (empty($outputStr)) {
                throw new \Exception("命令执行成功但没有输出");
            }
            
            // 解析JSON输出
            $result = json_decode($outputStr, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("JSON解析错误: " . json_last_error_msg() . "\n原始输出: " . $outputStr);
            }
            
            if (!is_array($result) || empty($result)) {
                throw new \Exception("解析结果格式错误: 预期数组但得到 " . gettype($result));
            }

            // 准备返回数据
            $responseData = [
                'url' => $url,
                'parse_time' => date('Y-m-d H:i:s'),
                'client_ip' => $request->getRealIp(),
                'result' => $result[0]
            ];

            // 存入缓存
            self::$cache[$cacheKey] = [
                'data' => $responseData,
                'expire' => $now + self::$cacheExpire,
                'created_at' => $now
            ];
            
            return json([
                'code' => 200,
                'msg' => '解析成功',
                'data' => $responseData
            ]);
        } catch (\Exception $e) {
            return json([
                'code' => 500,
                'msg' => '解析失败：' . $e->getMessage(),
                'data' => null
            ]);
        }
    }

    /**
     * 获取缓存列表
     * @param Request $request
     * @return \support\Response
     */
    public function cacheList(Request $request)
    {
        try {
            // 获取分页参数
            $page = max(1, intval($request->get('page', 1)));
            $perPage = max(1, intval($request->get('per_page', self::$perPage)));
            
            // 清理过期缓存
            $now = time();
            foreach (self::$cache as $key => $item) {
                if ($item['expire'] <= $now) {
                    unset(self::$cache[$key]);
                }
            }
            
            // 准备缓存数据
            $cacheData = [];
            foreach (self::$cache as $key => $item) {
                $cacheData[] = [
                    'url' => $item['data']['url'],
                    'parse_time' => $item['data']['parse_time'],
                    'created_at' => date('Y-m-d H:i:s', $item['created_at']),
                    'expire_at' => date('Y-m-d H:i:s', $item['expire']),
                    'expires_in' => $item['expire'] - $now,
                    'size' => strlen(json_encode($item['data']))
                ];
            }
            
            // 按创建时间倒序排序
            usort($cacheData, function($a, $b) {
                return strtotime($b['parse_time']) - strtotime($a['parse_time']);
            });
            
            // 计算分页
            $total = count($cacheData);
            $totalPages = ceil($total / $perPage);
            $page = min($page, $totalPages);
            
            // 获取当前页数据
            $start = ($page - 1) * $perPage;
            $items = array_slice($cacheData, $start, $perPage);
            
            return json([
                'code' => 200,
                'msg' => '获取成功',
                'data' => [
                    'items' => $items,
                    'pagination' => [
                        'total' => $total,
                        'per_page' => $perPage,
                        'current_page' => $page,
                        'total_pages' => $totalPages
                    ],
                    'summary' => [
                        'total_cached' => $total,
                        'total_size' => array_sum(array_column($cacheData, 'size')),
                        'cache_expire' => self::$cacheExpire . 's'
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return json([
                'code' => 500,
                'msg' => '获取缓存列表失败：' . $e->getMessage(),
                'data' => null
            ]);
        }
    }
}
