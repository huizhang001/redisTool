<?php
include 'config.php';
 
class Redistool{
	//redis句柄
	private $redis = NULL;
	/**
	 * 
	 */
	public function __construct(){
		$this->redis = new Redis();
		$this->redis->connect(HOST,PORT,OVERTIME);
	}
	//===================string========================
	/**
	 * 创建一个string类型的缓存
	 * @param string  $key   [字段的键]
	 * @param string  $value [值]
	 * @param integer $time  [过期时间]
	 */
	public function set_string($key = '',$value = '',$time = 600){
		return $this->redis->set($key,$value,$time);
	}
	/**
	 * 获取string类型的缓存
	 * @param  [type] $key [字段的值]
	 */
	public function get_string($key){
		return $this->redis->get($key);
	}
	/**
	 * 创建多个string类型的缓存（以数组方式传进来。一次设置多个）
	 * @param array   $array [要被写入缓存的数组]
	 * @param integer $time  [过期时间]
	 */
	public function set_string_arr($array = [],$time = 600){
		foreach ($array as $key => $value) {
			$res = $this->redis->set($key,$value,$time);
			if(!$res)
				return 'set string error';
		}
	}
	/**
	 * 将指定string类型的缓存的值自增
	 * @param  string $key [字段的键]
	 * @param  string $num [自增的数值]
	 */
	public function string_inc($key = '',$num = ''){
		if(!is_numeric($num))
			return 'the second argrement is a number';
		return $this->redis->incrby($key,$num);
	}
	/**
	 * 将指定string类型的缓存的值自减少
	 * @param  string $key [字段的键]
	 * @param  string $num [自减的数值]
	 */
	public function string_dec($key = '',$num = ''){
		if(!is_numeric($num))
			return 'the second argrement is a number';
		return $this->redis->decrby($key,$num);
	}
	/**
	 * 在指定string类型的缓存末尾添加字符串。
	 * @param  string $key   [字段的键]
	 * @param  string $value [值]
	 */
	public function string_append($key = '',$value = ''){
		return $this->redis->append($key,$value);
	}
	//===================hash============================
	/**
	 * 创建一个hash类型的缓存
	 * @param string $name  [hash名]
	 * @param array  $array [要被写入缓存的数组]
	 */
	public function set_hash($name = '',$array = []){
		$flag = 1;
		foreach ($array as $key => $value) {
			$res = $this->redis->hSet($name,$key,$value);
			if(!$res)
				$flag = 0;
		}
		if($flag)
			return 1;
		else
			return 'the hash has areadly created,but you can still add the key and the value';
	}
	/**
	 * 获取一个hash类型的缓存
	 * @param  string $name [hash名]
	 */
	public function get_hash($name = ''){
		return $this->redis->hGetAll($name);
	}
	/**
	 * 获取指定一个hash类型中指定key的值
	 * @param  string $name [hash名]
	 * @param  string $key  [字段的键]
	 */
	public function get_hash_value($name = '',$key = ''){
		return $this->redis->hGet($name,$key);
	}
	/**
	 * 为hash类型中的指定key的整形数值进行加减（正数加负数减）
	 * @param  string  $name [hash名]
	 * @param  string  $key  [字段的键]
	 * @param  integer $num  [加减数]
	 */
	public function hash_add_sub($name = '',$key = '',$num = 0){
		return $this->redis->hinCrby($name,$key,$num);
	}
	/**
	 * 删除一个hash类型缓存中的字段
	 * @param  string $name [hash名]
	 * @param  string $key  [字段的键]
	 */
	public function rm_hash_key($name = '',$key = ''){
		return $this->redis->hDel($name,$key);
	}
	//===================list=========================
	/**
	 * 创建一个list类型的缓存
	 * @param string $name  [list名]
	 * @param string $value [值]
	 */
	public function set_list($name = '',$value = ''){
		return $this->redis->lPush($name,$value);
	}
	/**
	 * 获取指定list范围中的list类型缓存
	 * 默认返回全部的值
	 * @param  string  $name  [list名]
	 * @param  integer $begin [开始的索引]
	 * @param  integer $end   [结束的索引]
	 */
	public function get_list_range($name = '',$begin = 0,$end = 1){
		return $this->redis->lRange($name,$begin,$end);
	}
	/**
	 * 获取list的第一个元素。如果没有元素则阻塞列表直到等待超时或发现可弹出元素(就是新进入List的元素)为止
	 * @param  string  $name [list名]
	 * @param  integer $time [超时时间]
	 */
	public function getrm_listbegin_block($name = '',$time = 60){
		return $this->redis->bLpop($name,$time);
	}
	/**
	 * 获取list的最后一个元素。如果没有元素则阻塞列表直到等待超时或发现可弹出元素(就是新进入List的元素)为止
	 * @param  string  $name [list名]
	 * @param  integer $time [超时时间]
	 */
	public function getrm_listend_block($name = '',$time = 60){
		return $this->redis->bRpop($name,$time);
	}
	/**
	 * 获取指定list的长度
	 * @param  string $name [list名]
	 */
	public function get_list_length($name = ''){
		return $this->redis->lLen($name);
	}
	/**
	 * 截取指定list范围的值
	 * @param  string  $name  [list名]
	 * @param  integer $begin [开始的索引]
	 * @param  integer $end   [结束的索引]
	 */
	public function trim_list($name = '',$begin = 0,$end = 0){
		return $this->redis->lTrim($name,$begin,$end);
	}
	/**
	 * 根据count的值，移除与value相等的元素
	 * count > 0 : 从表头开始向表尾搜索，移除与 value 相等的元素，数量为 count 
	 * count < 0 : 从表尾开始向表头搜索，移除与 value 相等的元素，数量为 count 的绝对值
	 * count = 0 : 移除表中所有与 value 相等的值
	 * @param  string  $name  [list名]
	 * @param  integer $count [数量]
	 * @param  string  $value [值]
	 */
	public function rm_value_list($name = '',$count = 0,$value = ''){
		return $this->redis->lRem($name,$count,$value);
	}
	//==========================set============================\
	/**
	 * 创建一个set缓存
	 * @param string $name  [set名]
	 * @param string $value [值]
	 */
	public function set_set($name = '',$value = ''){
		return $this->redis->sAdd($name,$value);
	}
	/**
	 * 获取set中的成员数
	 * @param  string $name [set名]
	 */
	public function get_num_set($name = ''){
		return $this->redis->sCard($name);
	}
	/**
	 * 获取set中的所有成员
	 * @param  string $name [set名]
	 */
	public function get_set($name = ''){
		return $this->redis->sMembers($name);
	}
	/**
	 * 将name1和name2进行比较，返回name1中不同于name2的值（name2的值不会返回）
	 * @param  string $name1 [要比较的set]
	 * @param  string $name2 [要比较的set]
	 */
	public function contrast_set($name1 = '',$name2 = ''){
		return $this->redis->sDiff($name1,$name2);
	}
	/**
	 * 将name1和name2进行比较，存入name1中不同于name2的值（name2的值不会被存入）
	 * @param  string $store [要存储进去的缓存名（如果已经存在将被覆盖）]
	 * @param  string $name1 [要比较的set]
	 * @param  string $name2 [要比较的set]
	 */
	public function contrast_store_set($store = '',$name1 = '',$name2 = ''){
		return $this->redis->sDiffStore($store,$name1,$name2);
	}
	/**
	 * 获取指定两个set的交集
	 * @param  string $name1 [要比较的set]
	 * @param  string $name2 [要比较的set]
	 */
	public function same_set($name1 = '',$name2 = ''){
		return $this->redis->sinter($name1,$name2);
	}
	/**
	 * 将name1和name2进行比较，存入name1与name2的交集
	 * @param  string $store [要存储进去的缓存名（如果已经存在将被覆盖）]
	 * @param  string $name1 [要比较的set]
	 * @param  string $name2 [要比较的set]
	 */
	public function same_store_set($store = '',$name1 = '',$name2 = ''){
		return $this->redis->sinterStore($store,$name1,$name2);
	}
	/**
	 * 判断value是否在指定的set内
	 * @param  string  $name  [set名]
	 * @param  string  $value [值]
	 * @return integer [true false]
	 */
	public function is_have_set($name = '',$value = ''){
		return $this->redis->sisMember($name,$value);		
	}
	/**
	 * 在指定的set中获取其中的随机值
	 * @param  string  $name  [set值]
	 * @param  integer $count [要获取个数]
	 */
	public function rand_set($name = '',$count = 0){
		return $this->redis->sRandMember($name,$count);
	}
	/**
	 * 移除set中的随机元素（随机个数由count决定）。移除时返回该元素（可以用来抽奖）
	 * @param  string  $name  [set值]
	 * @param  integer $count [个数]
	 */
	public function rm_get_set($name = '',$count = 0){
		return $this->redis->sPop($name,$count);
	}
	/**
	 * 移除set中的元素一个或多个元素
	 * @param  string $name  [set名]
	 * @param  string or array $value [值]
	 */
	public function rm_set($name = '',$value = ''){
		if(is_array($value)){
			$i = 0;
			foreach ($value as $key) {
				$this->redis->sRem($name,$key);
				$i++;
			}
			return $i;
		}
		return $this->redis->sRem($name,$value);
	}
	/**
	 * 将两个set的值合并到一起(自动去重)
	 * @param  string $name1 [要合并的set]
	 * @param  string $name2 [要合并的set]
	 */
	public function sun_set($name1 = '',$name2 = ''){
		return $this->redis->sunion($name1,$name2);
	}
	/**
	 * 将两个set的值合并到一起并存储到另一个set中(自动去重，如果另一个set已经存在，则覆盖)
	 * @param  string $store [要存储到的set]
	 * @param  string $name1 [要合并的set]
	 * @param  string $name2 [要合并的set]
	 */
	public function sun_stroe_set($store = '',$name1 = '',$name2 = ''){
		return $this->redis->sunionStore($store,$name1,$name2);
	}
	/**
	 * 迭代（个人理解可以模糊搜索指定set中的值）
	 * @param  string  $name    [set名]
	 * @param  string  $pattern [要搜索的值。格式:xxx*或者xxx]
	 * @param  integer $cursor  [暂时不知道有什么用]
	 *注意：如果值开头为数字就会搜索不到
	 */
	public function iter_set($name = '',$pattern = '',$cursor = 1){
		return $this->redis->sScan($name,$cursor,$pattern);
	}
	//====================sorted set===========================
	/**
	 * 将一个值加入到sorted set中
	 * @param string  $name  [sorted set名]
	 * @param integer $score [分数（sorted set根据分数从小到大排序）]
	 * @param string  $value [值]
	 */
	public function set_sorted($name = '',$score = 0,$value = ''){
		return $this->redis->zAdd($name,$score,$value);
	}
	/**
	 * 获取一个sorted set中指定索引范围的值（默认是全部返回）
	 * @param  string  $name       [sorted set名]
	 * @param  integer $begin      [开始的范围]
	 * @param  integer $end        [结束的范围]
	 */
	public function range_sorted($name = '',$begin = 0,$end = -1,$withscores = 0){
		return $this->redis->zRange($name,$begin,$end,$withscores);
	}
	/**
	 * 获取一个sorted set中指定分数范围的值。 
	 * @param  string  $name       [sorted set名]
	 * @param  integer $begin      [开始的范围]
	 * @param  integer $end        [结束的范围]
	 */
	public function range_score_sorted($name = '',$begin = '-inf',$end = '+inf'){
		return $this->redis->zRangeByScore($name,$begin,$end);
	}
	/**
	 * 获取指定sorted set中指定的值的索引(从0开始的)
	 * @param  string $name  [sorted set名]
	 * @param  string $value [值]
	 */
	public function get_index_sorted($name = '',$value = ''){
		return $this->redis->zRank($name,$value);
	}
	/**
	 * 获取指定sorted set值的数量
	 * @param  string $name [sorted set名]
	 */
	public function get_num_sorted($name = ''){
		return $this->redis->zCard($name);
	}
	/**
	 * 获取sorted set指定范围中的值的数量，包括开始和结束
	 * @param  string  $name  [sorted set名]
	 * @param  integer $begin [开始范围]
	 * @param  integer $end   [结束范围]
	 */
	public function range_num_sorted($name = '',$begin = 0,$end = 0){
		return $this->redis->zCount($name,$begin,$end);
	}
	/**
	 * 获取指定分数区间的值（这个是开始分数在后，结束分数在前，从大到小排）
	 * @param  string  $name  [sorted set名]
	 * @param  integer $end   [结束分数]
	 * @param  integer $begin [开始分数]
	 */
	public function score_range_sorted($name = '',$end = 0,$begin = 0){
		return $this->redis->zRevRangeByScore($name,$end,$begin);
	}
	/**
	 * 获取指定区间内的成员（从大到小排列）
	 * @param  string  $name  [sorted set名]
	 * @param  integer $begin [开始索引]
	 * @param  integer $end   [结束索引]
	 */
	public function score_inc_range_sorted($name = '',$begin = 0,$end = 0){
		return $this->redis->zRevRange($name,$begin,$end);
	}
	/**
	 * 获取成员的排名（0最高，按照分数来排名）
	 * @param  string $name  [sorted set名]
	 * @param  string $value [值]
	 */
	public function rank_sorted($name = '',$value = ''){
		return $this->redis->zRevRank($name,$value);
	}
	/**
	 * 给sorted set指定值加上增量（增量为负就是减）。如果指定值不存在，那就新增一个，并且给这个分数为增量
	 * @param  string  $name  [sorted set名]
	 * @param  integer $inc   [增量]
	 * @param  string  $value [值]
	 * @return  int [该值现在的分数]
	 */
	public function inc_score_sorted($name = '',$inc = 0,$value = ''){
		return $this->redis->zIncrBy($name,$inc,$value);
	}
	/**
	 * 对比两个sorted set并把他们相同的值（交集）存储在新的sorted set中
	 * @param  string $store [要存储的新的sorted set]
	 * @param  array  $array [要对比的几个sorted set都放在这里]
	 */
	public function same_store_sorted($store = '',$array = []){
		return $this->redis->zInterStore($store,$num);
	}
	/**
	 * 将两个sorted set的值合并到一起并存储到另一个sorted set中(自动去重，如果另一个sorted set已经存在，则覆盖)
	 * @param  string $store [要存储到的sorted set]
	 * @param  string $name1 [要合并的sorted set]
	 * @param  string $name2 [要合并的sorted set]
	 */
	public function sun_stroe_sorted($store = '',$arr = []){
		return $this->redis->zunionStore($store,['test1','test2']);
	}
	/**
	 * 删除指定sorted set中的指定值
	 * @param  string $name  [sorted set名]
	 * @param  string or value $value [值]
	 */
	public function rm_sorted($name = '',$value = ''){
		return $this->redis->zRem($name,$value);
	}
	/**
	 * 删除指定sorted set中的指定索引区间的所有成员
	 * @param  string  $name  [sorted set名]
	 * @param  integer $begin [删除开始的索引]
	 * @param  integer $end   [删除结束的索引]
	 */
	public function rm_index_sorted($name = '',$begin = 0,$end = 0){
		return $this->redis->zRemRangeByRank($name,$begin,$end);
	}
	/**
	 * 删除指定sorted set中，指定分数区间的所有成员
	 * @param  string  $name  [sorted set]
	 * @param  integer $begin [开始的分数]
	 * @param  integer $end   [结束的分数]
	 */
	public function rm_score_sorted($name = '',$begin = 0,$end = 0){
		return $this->redis->zRemRangeByScore($name,$begin,$end);
	}
	//=======================remove============================
	/**
	 * 删除指定的缓存
	 * @param  string $key [指定的缓存名]
	 */
	public function remove($key = ''){
		return $this->redis->del($key);
	}
}
$redis = new Redistool();
?>
