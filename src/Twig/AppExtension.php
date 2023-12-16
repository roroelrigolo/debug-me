<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('agoCustom', [AppRuntime::class, 'AgoCustom']),
            new TwigFilter('getContentCommentArray', [AppRuntime::class, 'getContentCommentArray']),
            new TwigFilter('html_entity_decode', 'html_entity_decode', ['is_safe' => ['html']]),
        ];
    }

}