<?php

namespace App\CMTwig;

use DOMDocument;
use Twig\TwigFunction;
use Durlecode\EJSParser\Parser;
use Psr\Container\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Serializer\Encoder\JsonEncode;

class TinymceExtension extends AbstractExtension
{

    private $roles, $container;
    public function __construct(Security $security, ContainerInterface $container)
    {
        $this->container = $container;
        if ($security->getUser() !== null)
            $this->roles = ($security->getUser()->getRoles());
    }




    public function
    getFunctions(): array
    {
        return [
            new TwigFunction('tmcerender', [$this, 'tmcerender', ['is_safe' => ['html']]]),
            new TwigFunction('tmcefirstImage', [$this, 'tmcefirstImage', ['is_safe' => ['html']]]),
            new TwigFunction('tmcefirstHeader', [$this, 'tmcefirstHeader', ['is_safe' => ['html']]]),
            new TwigFunction('tmcefirstText', [$this, 'tmcefirstText', ['is_safe' => ['html']]]),


        ];
    }

    public function tmcerender($texte)
    {
        return $texte;
        //ajout des finctionnalitées propre à mickcrud
        //travaille sur les images en ajoutant un filtre liip
    }
    public function tmcefirstImage($texte)
    {
        $htmlDom = new DOMDocument;
        @$htmlDom->loadHTML($texte);
        $img = $htmlDom->getElementsByTagName('img')[0];
        if ($this->roles != null or substr(html_entity_decode($value->data->caption), 0, 2) != '¤')
            return $img->getAttribute('src');
    }
    public function tmcefirstHeader($json)
    {

        return 'à faire';
    }
    public function tmcefirstText($texte)
    {
        dump($texte);
        $pos = strpos($texte, '<p><!-- pagebreak --></p>');
        if ($pos !== false)
            return strip_tags(substr($texte, 0, $pos));
        else
            return strip_tags($texte);
    }
}
