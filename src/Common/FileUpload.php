<?php
/**
 * Created by PhpStorm.
 * User: liuhui1
 * Date: 2018/4/2
 * Time: 10:33
 */

namespace PHPStack\Common;


class FileUpload
{

    /**
     * 文件上传处理,支持分片上传
     */
    public function upload()
    {

        $chunk = intval($_POST['chunk']);
        $chunks = intval($_POST['chunks']);

        $file = $_FILES['upfile'];

        if (!isset($file['error']) || is_array($file['error'])) {
            exit("invalid args.");
        }

        if ($file['error'] != UPLOAD_ERR_OK) {
            exit("upload fail,code:".$file['error']);
        }

        $dir = ROOT_PATH . 'uploads/';

        if (!file_exists($dir)) {
            mkdir($dir,0777,true);
        }

        $filepath = $dir . $file['name'] . '.part';

        $in = fopen($file['tmp_name'],'r');
        $out = fopen($filepath,'a');

        while ($data = fread($in, 4096)) {
            fwrite($out, $data);
        }

        fclose($in);
        fclose($out);

        if ($chunks <= 1 || $chunk == $chunks) {
            rename($filepath, substr($filepath,0,-5));
        }
        
    }

}