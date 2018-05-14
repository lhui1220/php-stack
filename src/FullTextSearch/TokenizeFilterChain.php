<?php
/**
 * Created by PhpStorm.
 * User: liuhui1
 * Date: 2018/4/24
 * Time: 10:49
 */

namespace PHPStack\FullTextSearch;


class TokenizeFilterChain
{

    private $filters;

    private $index; //当前执行的过滤器索引

    /**
     * 增加一个过滤器到过滤器链
     *
     * @param TokenizeFilter $filter
     *
     * @return $this
     */
    public function addFilter(TokenizeFilter $filter)
    {
        $this->filters[] = $filter;

        return $this;
    }

    /**
     * 词条处理
     *
     * @param $tokens 词条列表
     * @return array 处理过后的词条列表
     */
    public function filter($tokens)
    {
        foreach ($this->filters as $filter) {
            $tokens= $filter->filter($tokens,$this);
        }
        return $tokens;
    }
}