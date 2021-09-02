<?php
/**
 * 七牛云直播
 */

namespace App\Extend;

use Qiniu\Pili\Client;
use Qiniu\Pili\Mac;
use function Qiniu\Pili\RTMPPlayURL;
use function Qiniu\Pili\HLSPlayURL;
use function Qiniu\Pili\HDLPlayURL;
use function Qiniu\Pili\RTMPPublishURL;

class QnPili
{

    public static function getHub()
    {
        # 获取配置
        $ak = config('style.qnPili.ak');
        $sk = config('style.qnPili.sk');
        $hubName = config('style.qnPili.hubName');

        $mac = new Mac($ak, $sk);
        $client = new Client($mac);
        $hub = $client->hub($hubName);

        return $hub;
    }

    /**
     * 创建流
     * @param $name 流名称
     * @return array
     */
    public static function createStream($name)
    {
        $ak = config('style.qnPili.ak');
        $sk = config('style.qnPili.sk');
        $rtmp = config('style.qnPili.rtmp');
        $hubName = config('style.qnPili.hubName');
        $streamKey = config('style.qnPili.streamKey') . $name;

        $hub  = self::getHub();
        $resp = $hub->create($streamKey);

        /*$array = object_array($resp);
        $keys  = array_keys($array);
        foreach ($array as $key => $val) {
            if ($key == $keys[1]) {
                $result['data']['hub'] = $val;
            }
            if ($key == $keys[2]) {
                $result['data']['key'] = $val;
            }
            if ($key == $keys[3]) {
                $result['data']['base_url'] = $val;
            }
        }*/

        $data['push_url'] = RTMPPublishURL($rtmp, $hubName, $streamKey, 3600, $ak, $sk);
        return successMsg($data);
    }

    /**
     * 获取直播列表
     */
    public static function getLiveList()
    {
        $streamKey = config('style.qnPili.streamKey');
        $hub  = self::getHub();
        $resp = $hub->listLiveStreams($streamKey, 1, "");

        return successMsg($resp);
    }

    /**
     * 获取播放地址
     */
    public static function getPlayUrl($name, $type = 1)
    {
        $rtmpLive  = config('style.qnPili.rtmpLive');
        $hlsLive   = config('style.qnPili.hlsLive');
        $hdlLive   = config('style.qnPili.hdlLive');
        $hubName   = config('style.qnPili.hubName');
        $streamKey = config('style.qnPili.streamKey') . $name;
        switch ($type) {
            case 1:    # RTMP直播放址
                $url = RTMPPlayURL($rtmpLive, $hubName, $streamKey);
                break;
            case 2:    # HLS直播地址
                $url = HLSPlayURL($hlsLive, $hubName, $streamKey);
                break;
            case 3:    # HDL直播地址
                $url = HDLPlayURL($hdlLive, $hubName, $streamKey);
                break;
            default:
                $url = "";
                break;
        }
        return $url;
    }
}
