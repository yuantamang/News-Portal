<?php

namespace App\Enums;

enum AdvertisementPosition: string
{
    case HEADER = 'header';
    case SIDBAR = 'sidebar';
    case FOOTER = 'footer';
    case INLINE = 'inline';
    case POPUP = 'popup';
}
