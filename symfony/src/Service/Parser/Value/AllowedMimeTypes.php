<?php

namespace App\Service\Parser\Value;

class AllowedMimeTypes
{
    /** @var array  */
    public const TYPE_IMAGE = [
        'image/png',
        'image/jpeg',
        'image/jpg',
        'image/gif',
        'image/bmp',
        'svg+xml'
    ];

    /** @var array  */
    public const TYPE_CSV = [
        'text/csv', //RFC 4180
        'text/plain',
        'application/csv'
    ];
}
