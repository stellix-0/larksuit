# 集成飞书常用功能扩展

> 这是一个飞书PHP扩展，集合飞书认证、审批、消息、通讯录、日历、云空间等功能。

## 项目结构

    Auth                          认证
    Config                        配置
    Exception                     抛错
    Http                          Http请求
    Services                      服务集
  │   Approval                    审批服务
  │   Auth                        鉴权服务
  │   Calendar                    日历服务
  │   Contact                     联系人服务
  │   Drive                       云空间服务
  │   Message                     消息服务
  │ └  BaseService.php         
    Webhook                       回调服务
  └  LarkClient.php                

## 使用示例

### 审批场景

```
<?php

use Jeulia\Larksuit\LarkClient;

// 实例化飞书客户端
$larkClient = new LarkClient('YOUR_APP_ID', 'YOUR_APP_SECRET');

// 获取审批服务
$approvalService = $larkClient->approval();

// 提交审批, 参数内容参见：https://open.feishu.cn/document/server-docs/approval-v4/instance/create
$approvalService->createInstance($approvalForm->getFeishuApprovalCode(), [
    'user_id' => 'FEISHU_USER_ID',
    'form'    => 'FORM_CONTENT',
]);

```

### 机器人给人或这群组发送消息

```
<?php

use Jeulia\Larksuit\LarkClient;

// 实例化飞书飞书客户端
$larkClient = new LarkClient('YOUR_APP_ID', 'YOUR_APP_SECRET');

// 获取消息服务
$messageService = $larkClient->message();

// 发送消息指定人或者群组
$messageService->sendText('receive_id', 'Message', 'receive_id_type');

```

### 以webhook 的形式给机器人发送消息

```
<?php

use Jeulia\Larksuit\Robot;
use Jeulia\Larksuit\Enums\MsgType;

(new Robot('ROBOT_WEBHOOK'))->send(
            MsgType::TEXT->value,
           'Hahaaha'
        );

```
