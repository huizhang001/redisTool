<?php
session_start();
$redis = new Redis();
$redis->connect('127.0.0.1',6379);
	for($i = 0;$i < 100;$i++){
		$user = mt_rand(1,999);
		//判断队列长度
		if($redis->lLen('bookKill') < 10){
		//往redis中写入数据
			$redis->lPush('bookKill',$user);
			echo 'success!!';
		}else{
			echo 'you are late';
		}
	}
	


?>