# hyperf-tencent-meeting
腾讯会议 hyperf环境

使用教程
-------------------------------

### 安装
```shell
composer require zijing/hyperf-tencent-meeting -vvv
php bin/hyperf.php vendor:publish zijing/hyperf-tencent-meeting
```

### 测试案例
```php
<?php
namespace HyperfTest\Cases\Utils\Tencent\Meeting;

use Hyperf\Tencent\Meeting\Meeting;
use Hyperf\Utils\ApplicationContext;
use PHPUnit\Framework\TestCase;

class MeetingTest extends TestCase
{
    public $meeting;

    public $config;

    protected function setUp(): void
    {
        $this->config = config('tencent_meeting.restApi');
        $this->meeting = ApplicationContext::getContainer()->get(Meeting::class);
    }

    public function testCreateTencentMeeting()
    {
        $res = $this->meeting->send(
            $this->config['HOST'],
            $this->config['MEETING']['CREATE_MEETING']['METHOD'],
            $this->config['MEETING']['CREATE_MEETING']['URI'],
            [
            ]
        );
        var_dump($res);
    }

    public function testGetTencentMeeting()
    {
        $res = $this->meeting->send(
            $this->config['HOST'],
            $this->config['MEETING']['MEETING_LIST']['METHOD'],
            $this->config['MEETING']['MEETING_LIST']['URI'],
            [
                'userid' => 'meeting25ds',
                'instanceid' => 1,
            ]
        );

        var_dump($res);
    }
}
```

