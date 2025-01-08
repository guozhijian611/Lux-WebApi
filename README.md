# Lux Video Parser API

基于 [Lux](https://github.com/iawia002/lux) 的视频解析 API 服务，支持多种视频平台的链接解析。

## 特性

- 支持多种视频平台解析
- 内置缓存机制，提高解析效率
- RESTful API 设计
- 支持高并发访问
- 缓存管理和监控
- Docker 容器化部署

## 快速开始

### Docker 部署

1. 确保已安装 Docker 和 Docker Compose
2. 克隆项目：
```bash
git clone [https://github.com/guozhijian611/Lux-WebApi]
cd [Lux-WebApi]
```

3. 使用 Docker Compose 启动服务：
```bash
# 构建并启动容器
docker-compose up -d

# 查看容器状态
docker-compose ps

# 查看日志
docker-compose logs -f
```

4. 访问服务：
```
http://localhost:8787
```

### 手动部署

1. 确保已安装 PHP 8.0+ 和 Lux
2. 配置 webman 环境
3. 启动服务：
```bash
php start.php start
```

## API 接口

### 1. 基础信息接口

获取 API 服务的基本信息和支持的视频平台列表。

```
GET /
```

**响应示例：**
```json
{
    "code": 200,
    "msg": "API is Running",
    "data": {
        "Client IP": "127.0.0.1",
        "time": "2025-01-09 00:42:56",
        "content": "This is a Web API for LUX, Only allowed Lux -j Command",
        "lux Version": "0.24.1",
        "Usage": "Visit url /info?url=Your need URL",
        "Cache Time": "3600s",
        "Cache Size": 10,
        "Cache List": "url/cache/list?page=1&per_page=20",
        "support_list": [
            {"Site": "抖音", "URL": "https://www.douyin.com"},
            {"Site": "哔哩哔哩", "URL": "https://www.bilibili.com"}
            // ... 更多支持的网站
        ]
    }
}
```

### 2. 视频解析接口

解析指定视频 URL 的详细信息。

```
GET /info?url={video_url}
```

**参数：**
- `url`: 需要解析的视频 URL（必需）

**响应示例：**
```json
{
    "code": 200,
    "msg": "解析成功",
    "data": {
        "url": "https://example.com/video",
        "parse_time": "2025-01-09 00:42:56",
        "client_ip": "127.0.0.1",
        "result": {
            // 视频详细信息
        }
    }
}
```

### 3. 缓存列表接口

查看当前系统中的缓存数据。

```
GET /cache/list?page=1&per_page=20
```

**参数：**
- `page`: 页码（可选，默认：1）
- `per_page`: 每页显示数量（可选，默认：10）

**响应示例：**
```json
{
    "code": 200,
    "msg": "获取成功",
    "data": {
        "items": [
            {
                "url": "https://example.com/video",
                "parse_time": "2025-01-09 00:42:56",
                "created_at": "2025-01-09 00:42:56",
                "expire_at": "2025-01-09 01:42:56",
                "expires_in": 3600,
                "size": 1024
            }
        ],
        "pagination": {
            "total": 100,
            "per_page": 20,
            "current_page": 1,
            "total_pages": 5
        },
        "summary": {
            "total_cached": 100,
            "total_size": 102400,
            "cache_expire": "3600s"
        }
    }
}
```

## 错误码说明

- 200: 请求成功
- 400: 请求参数错误
- 500: 服务器内部错误

## 缓存机制

- 缓存时间：3600 秒（1小时）
- 缓存策略：URL MD5 作为缓存键
- 自动清理：过期缓存自动清理

## 性能优化

- 多进程处理请求
- 内存缓存加速
- 协程支持
- 请求队列管理

## Docker 环境说明

### 目录结构
```
.
├── Dockerfile          # Docker 镜像构建文件
├── docker-compose.yml  # Docker Compose 配置文件
├── app/               # 应用代码
├── config/            # 配置文件
├── runtime/           # 运行时文件
└── vendor/            # 依赖包
```

### 容器管理命令
```bash
# 构建镜像
docker-compose build

# 启动服务
docker-compose up -d

# 停止服务
docker-compose down

# 查看日志
docker-compose logs -f

# 进入容器
docker-compose exec webman bash

# 重启服务
docker-compose restart
```

### 配置说明
- 端口映射：8787:8787
- 时区设置：Asia/Shanghai
- 自动重启：enabled
- 数据持久化：使用 volume 挂载

## 注意事项

1. 请确保 Docker 和 Docker Compose 已正确安装
2. 生产环境建议使用 nginx 反向代理
3. 可以通过修改 docker-compose.yml 来调整端口映射
4. 容器内的日志位于 /www/runtime/logs 目录

## License

MIT License
