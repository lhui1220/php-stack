<?php
/**
 * Created by PhpStorm.
 * User: liuhui1
 * Date: 2018/4/24
 * Time: 10:49
 */

namespace PHPStack\FullTextSearch;

/**
 * Interface TokenizeFilter
 * 词条过滤器
 *
 * @package PHPStack\FullTextSearch
 */
interface TokenizeFilter
{

    /**
     * 词条处理
     *
     * @param $tokens 词条列表
     * @return array 处理过后的词条列表
     */
    public function filter($tokens, $filterChain);

}