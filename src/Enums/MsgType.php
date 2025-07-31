<?php

namespace Jeulia\Larksuit\Enums;

enum MsgType: string
{
    case TEXT = 'text';
    case POST = 'post';
    case IMAGE = 'image';
    case FILE = 'file';
    case AUDIO = 'audio';
    case MEDIA = 'media';
    case STICKER = 'sticker';
    case INTERACTIVE = 'interactive';
    case SHARE_CHAT = 'share_chat';
    case SHARE_USER = 'share_user';
    case SYSTEM = 'system';
}
