<?php
namespace shyiran\wordfilter\abstracts;
abstract class WordFilterAbstract
{
    protected $data = [];
    public function __construct (array $words)
    {
        $this->addWords ($words);
    }

    /**
     * 添加需要过滤的单词
     * @param string $word
     */
    abstract function addWord (string $word);

    /**
     * 删除过滤的单词
     *
     * @param string $word
     */
    abstract function removeWord (string $word);

    /**
     * 添加多个过滤词
     *
     * @param array $words
     */
    public function addWords ($words)
    {
        foreach ($words as $word) {
            $this->addWord ($word);
        }
    }

    public function clear ()
    {
        $this->data = [];
    }

    /**
     * 返回所有匹配到的词
     *
     * @param $content
     *
     * @return array
     */
    abstract function matchOne ($content);

    /**
     * 返回所有匹配到的词
     *
     * @param string $content
     *
     * @return array
     */
    abstract function matchAll ($content);

    /**
     *
     * 马赛克匹配到的词
     *
     * @param string $content 被搜索的字符串
     * @param array &$matches 引用，储存匹配到的词
     *
     * @return string
     */
    abstract function mosaic ($content, &$matches = []);

    /**
     * 测试是否包含匹配词
     *
     * @param string $content
     *
     * @return bool
     */
    public function test ($content)
    {
        return $this->matchOne ($content) ? true : false;
    }
}
