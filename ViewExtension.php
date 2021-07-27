<?php

namespace App\CMTwig;

use DOMDocument;
use Twig\TwigFunction;
//composer require imal-h/pdf-box
use ImalH\PDFLib\PDFLib;
use Twig\Extension\AbstractExtension;

class ViewExtension extends AbstractExtension
{


    public function
    getFunctions(): array
    {
        return [
            new TwigFunction('voir', [$this, 'voir', ['is_safe' => ['html']]]),

        ];
    }

    /**
     * Method voir retourne une image base64 d'un fichier ou d'une image
     *
     * @param $file $file [fichier, path par défaut /app/public]
     *
     * @return void
     */
    public function voir($infile)
    {
        //pour prendre directement en public
        if ($infile != '') {
            if (!file_exists($infile)) {
                if (file_exists('/app/public' . $infile)) {
                    $file = '/app/public' . $infile;
                }
                if (file_exists('/app/public/' . $infile)) {
                    $file = '/app/public/' . $infile;
                }
                if (file_exists('/app/public/uploads/' . $infile)) {
                    $file = '/app/public/uploads/' . $infile;
                }
            }
        }
        if (isset($file)) {
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
                    break;
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
        } else
            return ('image non présente');
    }
}
