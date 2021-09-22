<?php

namespace App\CMTwig;

use Twig\TwigFunction;
use Psr\Container\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Symfony\Component\Security\Core\Security;
use App\CMTwig\editorjsSimpleHtmlParser\src\Parser;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Serializer\Encoder\JsonEncode;

class EditorjsExtension extends AbstractExtension
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
            new TwigFunction('ejsrender', [$this, 'ejsrender', ['is_safe' => ['html']]]),
            new TwigFunction('ejsfirstImage', [$this, 'ejsfirstImage', ['is_safe' => ['html']]]),
            new TwigFunction('ejsfirstHeader', [$this, 'ejsfirstHeader', ['is_safe' => ['html']]]),
            new TwigFunction('ejsfirstText', [$this, 'ejsfirstText', ['is_safe' => ['html']]]),


        ];
    }

    public function ejsrender($json)
    {
        $tabs = json_decode($json);
        //on liste les objets
        foreach ($tabs->blocks as $num => $tab) {
            $data = '';
            switch ($tab->type) {
                case 'paragraph':
                case 'header':
                    $data = $tab->data->text;
                    if (substr(html_entity_decode($data), 0, 2) == '¤') $tabs->blocks[$num]->data->text = substr($tab->data->text, 2);
                    break;
                case 'image':
                    $data = $tab->data->caption;
                    if (substr(html_entity_decode($data), 0, 2) == '¤') $tabs->blocks[$num]->data->caption = substr($tab->data->caption, 2);
                    $width = getimagesize(getcwd() . $tab->data->url)[0];
                    //limit width
                    if ($width > 1920) {
                        $imagineCacheManager = $this->container->get('liip_imagine.cache.manager');
                        $resolvedPath = $imagineCacheManager->getBrowserPath($tab->data->url, 'fullhd');
                        $tabs->blocks[$num]->data->url = $resolvedPath;
                    }
                    break;
            }
            //si pas le droit de voir on supprime
            if (strpos($data, '¤') !== false)
                if (substr(html_entity_decode($data), 0, 2) == '¤' and $this->roles == null)
                    unset($tabs->blocks[$num]);
        }

        $json = json_encode($tabs);
        $html = null;
        if ($tabs->blocks)
            $html = new \Twig\Markup(Parser::parse($json)->toHtml(), 'UTF-8');
        //ajout des finctionnalitées propre à mickcrud
        //travaille sur les images en ajoutant un filtre liip
        return $html;
    }
    public function ejsfirstImage($json)
    {

        $tab = json_decode($json)->blocks;
        foreach ($tab as $key => $value) {
            if ($value->type == 'image') {
                if ($this->roles != null or substr(html_entity_decode($value->data->caption), 0, 2) != '¤')
                    return $value->data->url;
            }
        }
        //return $html;
    }
    public function ejsfirstHeader($json)
    {
        $tab = json_decode($json)->blocks;
        foreach ($tab as $key => $value) {
            if ($value->type == 'header')
                if ($this->roles != null or substr(html_entity_decode($value->data->text), 0, 2) != '¤')
                    return strip_tags(str_replace('¤', '', $value->data->text));
        }
        //return $html;
    }
    public function ejsfirstText($json)
    {
        $tab = json_decode($json)->blocks;
        foreach ($tab as $key => $value) {
            if ($value->type == 'paragraph')
                if ($this->roles != null or substr(html_entity_decode($value->data->text), 0, 2) != '¤')
                    return strip_tags(str_replace('¤', '', $value->data->text));
        }
    }
}
