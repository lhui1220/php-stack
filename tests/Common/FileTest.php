<?php
/**
 * Created by PhpStorm.
 * User: liuhui1
 * Date: 2018/4/2
 * Time: 10:54
 */

namespace PHPStack\Common;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{

    public function testRW()
    {
        $in = fopen('./data/rw_in.dat','r');
        $out = fopen('./data/rw_out.dat','a');

        while ($data = fread($in, 4)) {
            fwrite($out, $data);
        }
        fclose($in);
        fclose($out);
    }

    public function testRead()
    {
        $in = fopen('./data/rw_in.dat','r');

        $data = false;
        $out = "";
        while ($data = fread($in, 4)) {
            $out .= $data;
        }
        echo $out;

        echo substr('11.part',0,-5);
    }

    public function testExportExcel()
    {

        $orders = [
            ['order_id'=>1,'order_sn'=>'201804180001','price' => 99.99,'user_id'=>100,'order_goods'=>[
                ['goods_id' => 1 ,'goods_name' => 'goods name 1','price' => 99.99,'qty'=>1]
            ]],
            ['order_id'=>2,'order_sn'=>'201804180002','price' => 98.88,'user_id'=>101,'order_goods'=>[
                ['goods_id' => 2 ,'goods_name' => 'goods name 2','price' => 90.00,'qty'=>1],
                ['goods_id' => 3 ,'goods_name' => 'goods name 3','price' => 4.44,'qty'=>2]
            ]]
        ];

//        $orders = [
//            ['order_id'=>1,'order_sn'=>'201804180001','price' => 99.99,'user_id'=>100],
//            ['order_id'=>2,'order_sn'=>'201804180002','price' => 98.88,'user_id'=>101]
//        ];

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
                    ->setCreator("liuhui")
                    ->setTitle("订单数据");

        $worksheet = $spreadsheet->getActiveSheet();

        $headData = [
            'order_id' => '订单ID',
            'order_sn' => '订单号',
            'price'     => '价格',
            'user_id'   => '用户ID',
//            'order_goods.goods_id' => '商品ID',
            'order_goods.goods_name' => '商品名称',
//            'order_goods.price' => '商品价格',
//            'order_goods.qty' => '商品数量',
        ];
        $headKeys = array_keys($headData);
        $headValues = array_values($headData);

        //add header
        foreach ($headValues as $col => $headField) {
            $worksheet->setCellValueByColumnAndRow($col+1, 1, $headField);
        }

        $row = 2;
        //add order
        foreach ($orders as $order) {
            $col = 1;
            $startMerge = false;
            foreach ($headKeys as $field) {
                if (strpos($field,'.') !== false) {
                    $subfields = explode('.',$field);
                    $startMergeRow = $row;
                    foreach ($order[$subfields[0]] as $goods) {
                        $worksheet->setCellValueByColumnAndRow($col, $row, $goods[$subfields[1]]);
                        $row++;
                    }
                    for ($i = 1; $i < $col; $i++) {
                        $worksheet->mergeCellsByColumnAndRow($i,$startMergeRow,$i,$row-1);
                    }
                } else {
                    $worksheet->setCellValueByColumnAndRow($col, $row, $order[$field]);
                }
                $col++;
            }
            $row++;
        }
        $date = date('YmdHis');
        $xls = new Xls($spreadsheet);
        $xls->save("orders-$date.xls");

    }

}