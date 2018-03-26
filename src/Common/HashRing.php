<?php
/**
 * Created by PhpStorm.
 * User: liuhui1
 * Date: 2018/3/26
 * Time: 9:13
 */

namespace PHPStack\Common;


class HashRing
{

    private $nodes;

    private $ring;

    private $ringSize;

    public function __construct()
    {
        $this->nodes = [];
        $this->ring = [];
        $this->ringSize = pow(2, 32) - 1;
        //初始化服务器
        for ($i = 0; $i < 5; $i++) {
            $this->nodes[] = ['host'=>'192.168.0.'.($i+1),'port'=>80,'data'=>[]];
        }

        //将服务器分布在环上
        foreach ($this->nodes as &$node) {
            $hash = $this->hash($node['host']);
            $this->ring[$hash] = $node;
        }
        unset($node);
    }

    public function set($key, $val)
    {
        $hash = $this->hash($key);

        echo "key_hash=$hash ";
        //顺时针寻找hash值最接近的服务器
        for ($i = $hash; $i < $this->ringSize; $i++) {
            if ($this->ring[$hash]) {
                $exists = false;
                foreach ($this->ring[$hash]['data'] as &$item) {
                    if ($item['key'] == $key) {
                        $item['val'] = $val;
                        echo "update key\n";
                        $exists = true;
                    }
                }
                unset($item);
                if (!$exists) {
                    $this->ring[$hash]['data'][] = ['key'=>$key,'val'=>$val];
                    echo "insert key\n";
                }
                break;
            }
        }
    }

    public function get($key)
    {
        $hash = $this->hash($key);

        //顺时针寻找hash值最接近的服务器
        for ($i = $hash; $i < $this->ringSize; $i++) {
            if ($this->ring[$hash]) {
                foreach ($this->ring[$hash]['data'] as $data) {
                    if ($data['key'] == $key) {
                      return $data['val'];
                    }
                }
                break;
            }
        }
        return null;
    }

    private function hash($key)
    {
        return intval(sprintf('%u',crc32($key)));
    }

}