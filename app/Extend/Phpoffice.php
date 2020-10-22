<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/7/14
 * Time: 14:38
 */

namespace App\Extend;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Phpoffice
{

    /**
     * 导入Excel文件
     * @param $file
     * @return mixed
     */
    public static function import($file)
    {
        if (empty($file)) {
            return errorMsg('请选择上传文件');
        }
        $error = $file->getError();
        if ($error > 0) {
            return errorMsg('文件上传失败');
        }
        # 验证后缀
        $limit = ['xls','xlsx'];
        $postfix = strtolower($file->getClientOriginalExtension());
        if ($postfix && !in_array($postfix, $limit)) {
            $str = implode(' | ',$limit);
            return errorMsg('只能上传 '. $str .' 格式的文件');
        }

        $reader = new Xlsx();
        $spreadsheet = $reader->load($file->getRealPath());
        $sheetData   = $spreadsheet->getActiveSheet()->ToArray();

        return successMsg($sheetData);
    }
}
