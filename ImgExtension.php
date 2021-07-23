<?php

namespace App\CMTwig;

use DOMDocument;
use Twig\TwigFilter;
use Twig\TwigFunction;
use ImalH\PDFLib\PDFLib;
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
            new TwigFunction('getico', [$this, 'getico', ['is_safe' => ['html']]])
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
        if (!$tooltip) {
            $tooltip = $alt;
        } else {
            $alt = $tooltip;
        }
        if (isset($image)) {
            $ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
            switch ($ext) {
                case 'jpg':
                case 'jpeg':
                case 'gif':
                case 'png':
                    $file = $image;
                    if ($modal !== false) {
                        $return =
                            "<a  data-toggle='popover-hover' style=\"cursor:zoom-in;\" data-img=\"" . $this->CacheManager->getBrowserPath($this->Package->getUrl($file), "grand") . "\">";
                    }
                    $return .= '
             <img title="' . $tooltip . '" src="' .  $this->CacheManager->getBrowserPath($this->Package->getUrl($file), "mini") . '"
             class="' . $class . '" style="' . $taille . ';' . $style . '" alt="' . ucfirst($alt) . '"';

                    if ($modal !== false) {
                        $return .= "/></a>";
                    } else {
                        $return .= 'data-toggle="tooltip" data-placement="top" title="' . $tooltip . '" /></a>';
                    }
                    return $return;
                    break;
                default:
                    return "<img src='" . $this->getico($image) . "'>";
                    break;
            }
        } else return 'image non trouvée';
    }
    /***************************************************************************************************
     *                                             GETICO                                              *
     ***************************************************************************************************/
    /**
     * getico return un html img avec une icone représentant l'extensio ndu fichier
     * si la taille est différente on met une taille à l'img
     *
     * @param   string  $file    fichier sur le disque
     * @param   int=32  $taille  
     *
     * @return  string  img base64
     */
    function getico($file, $taille = 32)
    {
        //pour prendre directement en public
        if (!file_exists($file)) {
            if (file_exists('/app/public' . $file)) {
                $file = '/app/public' . $file;
            }
            if (file_exists('/app/public/' . $file)) {
                $file = '/app/public/' . $file;
            }
            if (file_exists('/app/public/uploads/' . $file)) {
                $file = '/app/public/uploads/' . $file;
            }
        }

        $dom = new DOMDocument('1.0', 'utf-8');
        $adresse = '/app/vendor/wgenial/php-mimetypeicon/icons/scalable/' . str_replace('/', '-', mime_content_type($file)) . '.svg';
        $dom->load($adresse);
        $svg = $dom->documentElement;

        if (!$svg->hasAttribute('viewBox')) { // viewBox is needed to establish
            // userspace coordinates
            $pattern = '/^(\d*\.\d+|\d+)(px)?$/'; // positive number, px unit optional

            $interpretable =  preg_match($pattern, $svg->getAttribute('width'), $width) &&
                preg_match($pattern, $svg->getAttribute('height'), $height);

            if ($interpretable) {
                $view_box = implode(' ', [0, 0, $width[0], $height[0]]);
                $svg->setAttribute('viewBox', $view_box);
            } else { // this gets sticky
                throw new Exception("viewBox is dependent on environment");
            }
        }

        $svg->setAttribute('width', $taille);
        $svg->setAttribute('height', $taille);
        $nom = uniqid();
        $dom->save('/tmp/' . $nom . '.svg');
        // if (!file_exists('/app/vendor/wgenial/php-mimetypeicon/icons/' . $taille)) {
        $adresse = '/app/vendor/wgenial/php-mimetypeicon/icons/scalable/' . str_replace('/', '-', mime_content_type($file)) . '.svg';
        return "data:image/svg+xml;base64," . base64_encode($this->unescape(file_get_contents('/tmp/' . $nom . '.svg')));
        // }

        //ancien système avec image
        // $adresse = '/app/vendor/wgenial/php-mimetypeicon/icons/' . $taille . '/' . str_replace('/', '-', mime_content_type(getcwd() . $file)) . '.png';
        // $imageData = base64_encode(file_get_contents($adresse));
        // return 'data: ' . mime_content_type(getcwd() . $file) . ';base64,' . $imageData;
    }
    //pour le svg
    //nettoie le svg pour pouvoir le convertir en base64
    function unescape($str)
    {
        $ret = '';
        $len = strlen($str);
        for ($i = 0; $i < $len; $i++) {
            if ($str[$i] == '%' && $str[$i + 1] == 'u') {
                $val = hexdec(substr($str, $i + 2, 4));
                if ($val < 0x7f) {
                    $ret .= chr($val);
                } elseif ($val < 0x800) {
                    $ret .= chr(0xc0 | ($val >> 6)) . chr(0x80 | ($val & 0x3f));
                } else {
                    $ret .= chr(0xe0 | ($val >> 12)) . chr(0x80 | (($val >> 6) & 0x3f)) . chr(0x80 | ($val & 0x3f));
                }
                $i += 5;
            } elseif ($str[$i] == '%') {
                $ret .= urldecode(substr($str, $i, 3));
                $i += 2;
            } else {
                $ret .= $str[$i];
            }
        }
        return $ret;
    }
}
