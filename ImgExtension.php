<?php

namespace App\CMTwig;

use Twig\TwigFilter;
use Twig\TwigFunction;
use Symfony\Component\Asset\Packages;
use Twig\Extension\AbstractExtension;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

class ImgExtension extends AbstractExtension
{
    protected $Package, $CacheManager;

    public function __construct(Packages $Package, CacheManager $CacheManager)
    {
        $this->Package = $Package;
        $this->CacheManager = $CacheManager;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('img', [$this, 'img'], ['is_safe' => ['html']]),
            new TwigFunction('thumbnail', [$this, 'thumbnail'], ['is_safe' => ['html']]),
        ];
    }
    // renvoie directement une balise img avec son src avec plusieurs taille en fonction de la largeur d'écran
    // combiné avec liipimagine, supporte les class, les styles et le lazy
    public function img($image, $size = '', $class = '', $style = '', $tooltip = '')
    {
        $taille = '100%';
        if (substr($size, 0, strlen('col')) == 'col')
            $taille = strval(intval(intval(substr($size, 3)) * 100 / 12)) . 'vw';
        if (substr($size, -2) == 'vw')
            $taille = $size;
        if (substr($size, -1) == '%')
            $taille = $size;
        $tab = explode('/', $image);
        $alt = str_replace('_', ' ', explode('.', end($tab))[0]);
        $alt = str_replace('-', "'", $alt);
        $return = '
             <img src="' . $this->CacheManager->getBrowserPath($this->Package->getUrl($image), "lazy") . '" 
             data-srcset="
               ' . $this->CacheManager->getBrowserPath($this->Package->getUrl($image), "mini") . ' 100w,
              ' . $this->CacheManager->getBrowserPath($this->Package->getUrl($image), "petit") . ' 300w,
               ' . $this->CacheManager->getBrowserPath($this->Package->getUrl($image), "semi") . ' 450w,
             ' . $this->CacheManager->getBrowserPath($this->Package->getUrl($image), "moyen") . ' 600w,
             ' . $this->CacheManager->getBrowserPath($this->Package->getUrl($image), "grand") . ' 900w"
             class="lazyload ' . $class . '" data-sizes="auto"
            style="width:' . $taille . ';' . $style . '" alt="' . ucfirst($alt) . '"';
        $return .= 'data-toggle="tooltip" data-placement="top" title="' . $tooltip . '"';
        return $return . ' />';
    }
    // renvoie une image en mini de A00px de large par défaut
    //modal permet de cliquer sur l'image pour avoir un apercu en grand
    // possibilité de donner des tailles par exemple:height:100px
    // on peux donner des classes et des styles
    public function thumbnail($image, $modal = true, $tooltip = '', $size = '', $class = '', $style = '')
    {
        $return = '';
        if ($size) $taille = $size;
        else $taille = 'width:100px';
        $tab = explode('/', $image);
        $alt = str_replace('_', ' ', explode('.', end($tab))[0]);
        $alt = str_replace('-', "'", $alt);
        if ($modal !== false)
            $return =
                "<a data-toggle='popover-hover' data-img=\"" . $this->CacheManager->getBrowserPath($this->Package->getUrl($image), "grand") . "\">";

        $return .= '
             <img src="' .  $this->CacheManager->getBrowserPath($this->Package->getUrl($image), "mini") . '"
             class="' . $class . '" style="' . $taille . ';' . $style . '" alt="' . ucfirst($alt) . '"';
        $return .= 'data-toggle="tooltip" data-placement="top" title="' . $tooltip . '" />';
        if ($modal !== false)
            $return .= "</a>";
        return $return;
    }
}
