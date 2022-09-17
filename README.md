# 屏蔽词检测、过滤、提取 （PHP）
- 两种实现，TreeWordFilter和ArrayWordFilter，适用于不同的php环境

- TreeWordFilter：适用常驻内存的php环境、swoole、cli
- ArrayWordFilter ： 适用于Web环境，调用一两次就退出程序的。

# 使用方式

```php
// 加载你要的单词列表
$keywords = [
    '山羊',
    '草地'
];
if(PHP_SAPI === 'cli'){
    $wordFilter = new \shyiran\wordfilter\TreeWordFilter($keywords);
}else{
    $wordFilter = new \shyiran\wordfilter\ArrayWordFilter($keywords);
}
$content = '那边草地上,有一只山羊';

// '测试是否存在屏蔽词 bool:';
$bool = $wordFilter->test($content);
var_dump($bool);

// 捕获一个屏蔽词 string | null
$word = $wordFilter->matchOne($content);
var_dump($word);
// 捕获所有屏蔽词 array
$words = $wordFilter->matchAll($content);
var_dump($words);
// 给原句中的屏蔽词打上马赛克 string
$newContent = $wordFilter->mosaic($content);
var_dump($newContent);

// 打印结果
bool(true)
string(6) "草地"
array(2) {
  [0]=>
  string(6) "草地"
  [1]=>
  string(6) "山羊"
}
string(23) "那边**上,有一只**"

```

