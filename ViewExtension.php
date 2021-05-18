<?php

namespace App\Twig;

use DOMDocument;
use Twig\TwigFilter;
use Twig\TwigFunction;
use ImalH\PDFLib\PDFLib;
use Twig\Extension\AbstractExtension;
use WGenial\PHPMimeTypeIcon\PHPMimeTypeIcon;

class ViewExtension extends AbstractExtension
{


    public function
    getFunctions(): array
    {
        return [
            new TwigFunction('voir', [$this, 'voir', ['is_safe' => ['html']]]),
            new TwigFunction('getico', [$this, 'getico', ['is_safe' => ['html']]]),
        ];
    }

    /**
     * Method voir retourne une image base64 d'un fichier ou d'une image
     *
     * @param $file $file [fichier, path par défaut /app/public]
     *
     * @return void
     */
    public function voir($file)
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
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        switch ($ext) {
            case 'pdf':
                $nompdf = uniqid();
                $pdflib = new PDFLib();
                $pdflib->setPdfPath($file);
                $pdflib->setOutputPath('/tmp');
                $pdflib->setImageFormat(PDFLib::$IMAGE_FORMAT_JPEG);
                $pdflib->setDPI(150);
                $pdflib->setPageRange(1, $pdflib->getNumberOfPages());
                $pdflib->setFilePrefix($nompdf); // Optional
                $pdflib->convert();
                $file =  '/tmp/' . $nompdf . '1.jpg';
            case 'jpg':
            case 'jpeg':
            case 'gif':
            case 'png':
                $imageData = base64_encode(file_get_contents($file));
                return 'data:' . mime_content_type($file) . ';base64,' . $imageData;
                break;
            default:
                return $file;
                break;
        }
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
