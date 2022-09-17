<?php
namespace shyiran\wordfilter;
use shyiran\wordfilter\abstracts\WordFilterAbstract;
/**
 * 关键词过滤
 */
class ArrayWordFilter extends WordFilterAbstract
{

    public function __construct(array $words)
    {
        parent::__construct($words);
    }

    /**
     * @inheritdoc
     */
    public function addWord(string $word)
    {
        $this->data[$word] = "<<<{$word}>>>";
    }

    /**
     * @inheritdoc
     */
    public function removeWord(string $word){
        unset($this->data[$word]);
    }

    /**
     * @inheritdoc
     */
    public function matchOne($content)
    {
        $tpl = strtr($content,$this->data);
        if(strlen($tpl) === strlen($content)){
            return null;
        }
        preg_match('#<<<(.*?)>>>#',$tpl,$match);
        return $match[1]??null;
    }

    /**
     * @inheritdoc
     */
    public function test($content)
    {
        $tpl = strtr($content,$this->data);
        if(strlen($tpl) === strlen($content)){
            return false;
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function matchAll($content)
    {
        $tpl = strtr($content,$this->data);
        if(strlen($tpl) === strlen($content)){
            return [];
        }
        preg_match_all('#<<<(.*?)>>>#',$tpl,$match);
        return $match[1]??[];
    }

    /**
     * @inheritdoc
     */
    public function mosaic($content,&$matches = []){
        $matches = [];
        $tpl = strtr($content,$this->data);
        if(strlen($tpl) === strlen($content)){
            return $content;
        }
        $ret = preg_replace_callback('#<<<(.*?)>>>#',function($match) use (&$matches){
            $matches[] = $match[1];
            return str_repeat('*',mb_strlen($match[1],'utf-8'));
        },$tpl);
        return $ret ;
    }
}
