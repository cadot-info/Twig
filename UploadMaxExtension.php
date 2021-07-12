<?php

namespace App\CMTwig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class UploadMaxExtension extends AbstractExtension
{

    public function getFunctions(): array
    {
        return [
            new TwigFunction('upload_max', [$this, 'max']),
        ];
    }

    public function max()
    {
        $max_upload = (int)(ini_get('upload_max_filesize'));
        $max_post = (int)(ini_get('post_max_size'));
        $memory_limit = (int)(ini_get('memory_limit'));
        return (min($max_upload, $max_post, $memory_limit));
    }
}
