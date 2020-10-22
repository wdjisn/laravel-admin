<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/7/14
 * Time: 17:37
 */

namespace App\Jobs;

use App\Model\SmsNotify;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class Snotify implements ShouldQueue
{

    # 创建命令： php artisan make:job Snotify

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $notify;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(SmsNotify $notify)
    {
        $this->notify = $notify;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        var_dump($this->notify['id']);

        sleep(2);

        # TODO::发送短信通知

        $this->notify->status = 1;
        $this->notify->update();
    }
}
