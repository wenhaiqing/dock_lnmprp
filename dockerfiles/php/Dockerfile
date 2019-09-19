FROM php:7.2-fpm

# 按需安装php扩展
# 1.0.2 增加 bcmath, calendar, exif, gettext, sockets, dba, 
# mysqli, pcntl, pdo_mysql, shmop, sysvmsg, sysvsem, sysvshm 扩展
RUN docker-php-ext-install -j$(nproc) bcmath calendar exif gettext \
sockets dba mysqli pcntl pdo_mysql shmop sysvmsg sysvsem sysvshm

#切换源
#COPY ./sources.list /etc/apt/sources.list
#RUN cat /etc/apt/sources.list
#RUN rm -Rf /var/lib/apt/lists/*

# 1.0.7 增加 soap 扩展
RUN apt-get update -y && \
	apt-get install -y --no-install-recommends \ 
	libxml2-dev libtidy-dev libxslt1-dev zlib1g-dev libpng-dev git zip unzip \ 
	&& rm -r /var/lib/apt/lists/* \ 
	&& docker-php-ext-install -j$(nproc) soap gd
    
# 使用pecl安装扩展，记得在php.ini中添加配置项(grpc依赖于zlib1g-dev扩展)
#RUN pecl install grpc \
#RUN pecl install swoole-4.4.4 \
COPY ./swoole-4.4.4.tgz /home/swoole.tgz
RUN pecl install /home/swoole.tgz \
	&& docker-php-ext-enable swoole

# 如果扩展下载时间太长，可以先下载到本地，再copy到指定目录，再用pecl安装
COPY ./redis-5.0.2.tgz /home/redis.tgz
RUN pecl install /home/redis.tgz \
    && docker-php-ext-enable redis

# 安装composer(compoaer.phar需要先下载到本地)
ADD composer.phar /usr/local/bin/composer
RUN chmod 755 /usr/local/bin/composer