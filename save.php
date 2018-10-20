<?php
$con = mysqli_connect('127.0.0.1','root','','book');
$redis = new Redis();
$redis->connect('127.0.0.1',6379);
for($i = 0;$i < 10;$i++){
	//获取列表中的值
	$data = $redis->Rpop('bookKill');
	//如果缓存中没数据了，但还不够10个，说明没有被秒杀完。就跳出循环。
	if($data == null)
		break;
	//插入数据库。记得username是字符串要加上单引号，不然无法插入又记不了日志。。。
	$sql = 'insert into book (`username`) values(\''.$data.'\');';
	//插入数据,如果有错误信息写入到log日志中
	if(!mysqli_query($con,$sql)){
		file_put_contents('./dbErrorLog.txt', mysqli_error($con));
	}
}
?>