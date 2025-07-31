<?php

namespace Jeulia\Larksuit\Enums;

enum ReceiveIdType: string
{
    case OPEN_ID = 'open_id';
    case UNION_ID = 'union_id';
    case USER_ID = 'user_id';
    case EMAIL = 'email';
    case CHAT_ID = 'chat_id';
}
