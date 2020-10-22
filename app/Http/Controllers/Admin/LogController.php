<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/16
 * Time: 18:14
 */

namespace App\Http\Controllers\Admin;

use App\Model\ErrorLog;
use App\Service\LogService;

class LogController extends BaseController
{

    /**
     * 获取错误日志
     */
    public function getErrorLog()
    {
        $where['start']  = $this->requestArr['start'] ?? '';
        $where['end']    = $this->requestArr['end'] ?? '';
        $where['status'] = $this->requestArr['status'] ?? null;
        $perPage = $this->requestArr['per_page'] ?? 15;

        $data = LogService::getErrorLog($where, $perPage);

        # 统计未处理bug数量
        $data['error_count'] = LogService::getErrorCount(['status' => 0]);

        successReturn($data);
    }

    /**
     * 修改错误日志处理状态
     */
    public function editError()
    {
        $id = $this->requestArr['id'];
        $status = $this->requestArr['status'] == 1 ? 1 : 0;

        $result = ErrorLog::editError($id,$status);
        if (!$result) {
            errorReturn();
        }
        successReturn();
    }

    /**
     * 获取登录日志
     */
    public function getLoginLog()
    {
        $where['start']    = $this->requestArr['start'] ?? '';
        $where['end']      = $this->requestArr['end'] ?? '';
        $where['username'] = $this->requestArr['username'] ?? '';
        $perPage = $this->requestArr['per_page'] ?? 15;

        $data = LogService::getLoginLog($where,$perPage);

        successReturn($data);
    }

    /**
     * 获取访问日志
     */
    public function getRequestLog()
    {
        $page = $this->requestArr['page'] ?? 1;
        $perPage = $this->requestArr['per_page'] ?? 15;
        $file = base_path().'/storage/logs/request.log';

        # 获取文件总行数
        $rows = getFileRows($file);

        $list  = Array();
        $start = $rows - ($page - 1) * $perPage;
        $end   = $start - $perPage + 1;
        for ($i=$end;$i<=$start;$i++) {
            $content = getLine($file,$i);
            $list[] = json_decode($content,true);
        }
        $list = array_filter($list);
        $list = array_reverse($list);

        $data = [
            'current_page' => (int)$page,
            'data'         => $list,
            'last_page'    => ceil($rows/$perPage),
            'per_page'     => $perPage,
            'total'        => $rows
        ];
        successReturn($data);
    }
}
