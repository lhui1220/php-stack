<?php
/**
 * Created by PhpStorm.
 * User: liuhui1
 * Date: 2018/4/24
 * Time: 10:41
 */

namespace PHPStack\FullTextSearch;

/**
 * Interface Tokenizer
 * 分词策略接口
 *
 * @package PHPStack\FullTextSearch
 */
interface TokenizeStrategy
{

    public function tokenize($text);

}