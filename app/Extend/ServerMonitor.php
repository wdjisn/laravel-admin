<?php
/**
 * 服务器监控数据
 */

namespace App\Extend;

class ServerMonitor
{

    /**
     * 服务器运行时间
     * @return string
     */
    public function getUpTime()
    {
        if (false === ($str = file_get_contents("/proc/uptime"))) {
            return '';
        }

        $upTime = '';
        $str    = explode(" ", $str);
        $str    = trim($str[0]);
        $min    = $str / 60;
        $hours  = $min / 60;
        $days   = (int)($hours / 24);
        $hours  = $hours % 24;
        $min    = $min % 60;

        if ($days !== 0) {
            $upTime = $days . "天";
        }
        if ($hours !== 0) {
            $upTime .= $hours . "小时";
        }

        return $upTime . $min . "分钟";
    }

    /**
     * 内存信息
     * @param bool $bFormat
     * @return array
     */
    public function getMem(bool $bFormat = false)
    {
        if (false === ($str = file_get_contents("/proc/meminfo"))) {
            return Array();
        }

        preg_match_all("/MemTotal\s{0,}\:+\s{0,}([\d\.]+).+?MemFree\s{0,}\:+\s{0,}([\d\.]+).+?Cached\s{0,}\:+\s{0,}([\d\.]+).+?SwapTotal\s{0,}\:+\s{0,}([\d\.]+).+?SwapFree\s{0,}\:+\s{0,}([\d\.]+)/s", $str, $mems);
        preg_match_all("/Buffers\s{0,}\:+\s{0,}([\d\.]+)/s", $str, $buffers);

        $mtotal    = $mems[1][0] * 1024;
        $mfree     = $mems[2][0] * 1024;
        $mbuffers  = $buffers[1][0] * 1024;
        $mcached   = $mems[3][0] * 1024;
        $stotal    = $mems[4][0] * 1024;
        $sfree     = $mems[5][0] * 1024;
        $mused     = $mtotal - $mfree;
        $sused     = $stotal - $sfree;
        $mrealused = $mtotal - $mfree - $mcached - $mbuffers;   // 真实内存使用

        $rtn['mTotal']       = ceil($mtotal/1024/1024);
        $rtn['mRealUsed']    = ceil($mrealused/1024/1024);
        $rtn['mRealPercent'] = (floatval($mtotal) != 0) ? round($mrealused / $mtotal * 100, 2) : 0;      // 真实内存使用率
        return $rtn;

        /*$rtn['mTotal']         = !$bFormat ? $mtotal : $this->sizeFormat($mtotal, 1);
        $rtn['mFree']          = !$bFormat ? $mfree : $this->sizeFormat($mfree, 1);
        $rtn['mBuffers']       = !$bFormat ? $mbuffers : $this->sizeFormat($mbuffers, 1);
        $rtn['mCached']        = !$bFormat ? $mcached : $this->sizeFormat($mcached, 1);
        $rtn['mUsed']          = !$bFormat ? ($mtotal - $mfree) : $this->sizeFormat($mtotal - $mfree, 1);
        $rtn['mPercent']       = (floatval($mtotal) != 0) ? round($mused / $mtotal * 100, 1) : 0;
        $rtn['mRealUsed']      = !$bFormat ? $mrealused : $this->sizeFormat($mrealused, 1);
        $rtn['mRealFree']      = !$bFormat ? ($mtotal - $mrealused) : $this->sizeFormat($mtotal - $mrealused, 1);    // 真实空闲
        $rtn['mRealPercent']   = (floatval($mtotal) != 0) ? round($mrealused / $mtotal * 100, 1) : 0;                 // 真实内存使用率
        $rtn['mCachedPercent'] = (floatval($mcached) != 0) ? round($mcached / $mtotal * 100, 1) : 0;                  // Cached内存使用率
        $rtn['swapTotal']      = !$bFormat ? $stotal : $this->sizeFormat($stotal, 1);
        $rtn['swapFree']       = !$bFormat ? $sfree : $this->sizeFormat($sfree, 1);
        $rtn['swapUsed']       = !$bFormat ? $sused : $this->sizeFormat($sused, 1);
        $rtn['swapPercent']    = (floatval($stotal) != 0) ? round($sused / $stotal * 100, 1) : 0;

        return $rtn;*/
    }

    /**
     * 获取CPU使用率
     * @return array|float[]|int[]
     */
    public function getCPU()
    {
        $cpuinfo1 = $this->getCPUInfo();
        if ($cpuinfo1) {
            sleep(1);
            $cpuinfo2 = $this->getCPUInfo();

            $time    = $cpuinfo2['time'] - $cpuinfo1['time'];
            $total   = $cpuinfo2['total'] - $cpuinfo1['total'];
            $percent = round($time / $total, 4);
            $percent = $percent * 100;

            return ['total' => $percent,];
        }
        return Array();
    }

    /**
     * 获取系统负载
     * @return array|false|string[]
     */
    public function getLoad()
    {
        if (false === ($str = file_get_contents("/proc/loadavg"))) {
            return Array();
        }

        $loads = explode(' ', $str);
        if ($loads) {
            return [
                '1m'  => $loads[0],
                '5m'  => $loads[1],
                '15m' => $loads[2],
            ];
        }

        return Array();
    }

    /**
     * 获取CPU信息
     * @return array|false
     */
    private function getCPUInfo()
    {
        if (false === ($str = file_get_contents("/proc/stat"))) {
            return false;
        }

        $cpu  = Array();
        $mode = "/(cpu)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)/";
        preg_match_all($mode, $str, $cpu);
        $total = $cpu[2][0] + $cpu[3][0] + $cpu[4][0] + $cpu[5][0] + $cpu[6][0] + $cpu[7][0] + $cpu[8][0] + $cpu[9][0];
        $time  = $cpu[2][0] + $cpu[3][0] + $cpu[4][0] + $cpu[6][0] + $cpu[7][0] + $cpu[8][0] + $cpu[9][0];
        // or $cpu_rate =  (($out[2][0] + $out[3][0]) / ($out[4][0] + $out[5][0] + $out[6][0] + $out[7][0]))*100;

        return ['total' => $total, 'time' => $time];
    }

    /**
     * 获取网络数据
     * @param bool $bFormat
     * @return array
     */
    public function getNetwork(bool $bFormat = false)
    {
        $rtn = Array();
        $netstat = file_get_contents('/proc/net/dev');
        if (false === $netstat) {
            return [];
        }

        $bufe = preg_split("/\n/", $netstat, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($bufe as $buf) {
            if (preg_match('/:/', $buf)) {
                list($dev_name, $stats_list) = preg_split('/:/', $buf, 2);
                $dev_name = trim($dev_name);

                $stats = preg_split('/\s+/', trim($stats_list));
                $rtn[$dev_name]['name']       = $dev_name;
                $rtn[$dev_name]['in_rate']    = !$bFormat ? $stats[0] : $this->netSize($stats[0]);
                $rtn[$dev_name]['in_packets'] = $stats[1];
                $rtn[$dev_name]['in_errors']  = $stats[2];
                $rtn[$dev_name]['in_drop']    = $stats[3];

                $rtn[$dev_name]['out_traffic'] = !$bFormat ? $stats[8] : $this->netSize($stats[8]);
                $rtn[$dev_name]['out_packets'] = $stats[9];
                $rtn[$dev_name]['out_errors']  = $stats[10];
                $rtn[$dev_name]['out_drop']    = $stats[11];
            }
        }

        return $rtn;
    }

    /**
     * 磁盘信息
     * @param string $disk
     * @return array
     */
    public function getDisk(string $disk = 'sda', string $dir = '/dev/sda1')
    {
        $hddTotal = disk_total_space($dir);
        $hddFree  = disk_free_space($dir);
        $hddUsed  = $hddTotal - $hddFree;
        $hddPercent = (floatval($hddTotal) != 0) ? round($hddUsed / $hddTotal * 100, 2) : 0;

        $rtn = [
            'total'      => $hddTotal,
            'free'       => $hddFree,
            'used'       => $hddUsed,
            'percent'    => $hddPercent,
            'readbytes'  => 0,
            'writebytes' => 0,
            'readiops'   => 0,
            'writeiops'  => 0,
        ];

        try {
            $nowTime  = microtime(true);
            $diskStat = file_get_contents("/sys/class/block/$disk/stat");
            if ($diskStat) {
                $stats = preg_split('/\s+/', trim($diskStat));
                if ($stats) {
                    $cacheStats = Cache::get('mdisk:' . $disk);
                    if ($cacheStats) {
                        $rIO = $stats[0] - $cacheStats[0];
                        $rSectors = $stats[2] - $cacheStats[2];
                        $wIO = $stats[4] - $cacheStats[4];
                        $wSectors = $stats[6] - $cacheStats[6];
                        $timeMid = $nowTime - Cache::get('mdiskt:' . $disk);

                        $rtn['readiops']   = round($rIO / $timeMid, 2);
                        $rtn['writeiops']  = round($wIO / $timeMid, 2);
                        $rtn['readbytes']  = round($rSectors * 512 / $timeMid, 2);
                        $rtn['writebytes'] = round($wSectors * 512 / $timeMid, 2);
                    }
                    Cache::set('mdisk:' . $disk, $stats, 120);
                    Cache::set('mdiskt:' . $disk, $nowTime, 120);
                }
            }
        } catch (\Throwable $e) {

        }

        return $rtn;
    }

    /**
     * 大小格式化
     * @param $bytes
     * @param int $decimals
     * @return false|string
     */
    private function sizeFormat($bytes, $decimals = 2)
    {
        $quant = [
            'TB' => 1099511627776,  // pow( 1024, 4)
            'GB' => 1073741824,     // pow( 1024, 3)
            'MB' => 1048576,        // pow( 1024, 2)
            'KB' => 1024,           // pow( 1024, 1)
            'B ' => 1
        ];

        foreach ($quant as $unit => $mag) {
            if (doubleval($bytes) >= $mag) {
                return number_format($bytes / $mag, $decimals) . ' ' . $unit;
            }
        }

        return false;
    }

    public function netSize($size, $decimals = 2)
    {
        if ($size < 1024) {
            $unit = "Bbps";
        } else if ($size < 10240) {
            $size = round($size / 1024, $decimals);
            $unit = "Kbps";
        } else if ($size < 102400) {
            $size = round($size / 1024, $decimals);
            $unit = "Kbps";
        } else if ($size < 1048576) {
            $size = round($size / 1024, $decimals);
            $unit = "Kbps";
        } else if ($size < 10485760) {
            $size = round($size / 1048576, $decimals);
            $unit = "Mbps";
        } else if ($size < 104857600) {
            $size = round($size / 1048576, $decimals);
            $unit = "Mbps";
        } else if ($size < 1073741824) {
            $size = round($size / 1048576, $decimals);
            $unit = "Mbps";
        } else {
            $size = round($size / 1073741824, $decimals);
            $unit = "Gbps";
        }
        $size .= $unit;

        return $size;
    }
}
