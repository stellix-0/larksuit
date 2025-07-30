<?php

namespace Jeulia\Larksuit\Webhook;

enum EventType: string
{
    case APPROVAL          = 'approval';
    case APPROVAL_INSTANCE = 'approval_instance';
    case APPROVAL_CC       = 'approval_cc';
    case APPROVAL_TASK     = 'approval_task';
}