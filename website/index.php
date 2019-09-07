<?php
$conn = mysqli_connect('mysql','root','123456');
if ($conn) {
	echo "mysql连接成功";
}

$redis = new Redis();

$redis->connect('redis', 6379);
$redis->set('name','wenhaiqing1');
echo $redis->get('name');

phpinfo();