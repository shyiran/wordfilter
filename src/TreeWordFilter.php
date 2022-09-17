<?php

namespace shyiran\wordfilter;

use shyiran\wordfilter\abstracts\WordFilterAbstract;

/**
 * 关键词过滤
 */
class TreeWordFilter extends WordFilterAbstract
{
    /**
     * @inheritdoc
     */
    public function addWord (string $word)
    {
        $map = &$this->data;
        $chars = preg_split ('//u', $word, null, PREG_SPLIT_NO_EMPTY);
        $mbLen = count ($chars);
        for ($i = 0; $i < $mbLen; $i++) {
            $char = $chars[$i];
            if (!isset($map[$char])) {
                $map[$char] = [];
            }
            $map = &$map[$char];
        }
        $map['$$'] = true;
    }


    /**
     * @inheritdoc
     */
    public function removeWord (string $word)
    {
        $this->matchCallback ($word, function ($i, $j, $chars, &$node) {
            $node['$$'] = false;
        }, true);
    }


    /**
     * @inheritdoc
     */
    public function matchOne ($content)
    {
        $match = null;
        $this->matchCallback ($content, function ($i, $j, $chars) use (&$match) {
            $matchChars = [];
            for ($x = $i; $x <= $j; $x++) {
                $matchChars[] = $chars[$x];
            }
            $match = implode ('', $matchChars);
            return false;
        });
        return $match;
    }

    /**
     * @inheritdoc
     */
    public function matchAll ($content)
    {
        $matches = [];
        $this->matchCallback ($content, function ($i, $j, $chars) use (&$matches) {
            $matchChars = [];
            for ($x = $i; $x <= $j; $x++) {
                $matchChars[] = $chars[$x];
            }
            $matches[] = implode ('', $matchChars);
            return true;
        });
        return $matches;
    }

    /**
     * @inheritdoc
     */
    public function mosaic ($content, &$matches = [])
    {
        $matches = [];
        list($hit, $chars) = $this->matchCallback ($content, function ($i, $j, &$chars) use (&$matches) {
            $matchChars = [];
            for ($x = $i; $x <= $j; $x++) {
                $matchChars[] = $chars[$x];
                $chars[$x] = '*';
            }
            $matches[] = implode ('', $matchChars);
            return true;
        });
        return $hit ? implode ('', $chars) : $content;
    }

    /**
     * @param string $content 被搜索的字符串
     * @param callable $func ($i,$j,&$chars) 回调函数
     * @param bool $isFullMatch 是否完全匹配
     *
     * @return array
     */
    public function matchCallback ($content, callable $func, bool $isFullMatch = false)
    {
        $parent = &$this->data;
        $chars = preg_split ('//u', $content, null, PREG_SPLIT_NO_EMPTY);
        $msg_len = count ($chars);
        $level = $isFullMatch ? 1 : $msg_len;
        $hit = false;
        for ($i = 0; $i < $level; $i++) {
            for ($j = $i; $j < $msg_len; $j++) {
                $char = $chars[$j];
                if (!isset($parent[$char])) {
                    $parent = &$this->data;
                    break;
                }
                $parent = &$parent[$char];
                if (isset($parent['$$'])) {
                    if ($parent['$$'] === true) {
                        $hit = true;
                        $ret = $func($i, $j, $chars, $parent);
                        if ($ret === false) {
                            return [ $hit, $chars ];
                        }
                        $i = $j;
                    }
                    $parent = &$this->data;
                    break;
                }
            }
        }
        return [ $hit, $chars ];
    }
}
