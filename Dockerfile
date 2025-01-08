FROM php:8.2-cli

# 安装必要的系统依赖
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libzip-dev \
    zip \
    unzip \
    wget

# 安装 PHP 扩展
RUN docker-php-ext-install zip pcntl

# 安装 Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 安装 Lux
RUN wget https://github.com/iawia002/lux/releases/download/v0.24.1/lux_0.24.1_Linux_x86_64.tar.gz \
    && tar -xzf lux_0.24.1_Linux_x86_64.tar.gz \
    && mv lux /usr/bin/ \
    && chmod +x /usr/bin/lux \
    && rm lux_0.24.1_Linux_x86_64.tar.gz

# 设置工作目录
WORKDIR /www

# 复制项目文件
COPY . /www

# 安装项目依赖
RUN composer install --no-dev --optimize-autoloader

# 设置权限
RUN chmod -R 777 /www/runtime

# 暴露端口
EXPOSE 8787

# 启动命令
CMD ["php", "start.php", "start"]
