<?php

namespace App\CMTwig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Durlecode\EJSParser\Parser;

class EditorjsExtension extends AbstractExtension
{


    public function
    getFunctions(): array
    {
        return [
            new TwigFunction('ejsrender', [$this, 'ejsrender', ['is_safe' => ['html']]]),
            new TwigFunction('firstImage', [$this, 'firstImage', ['is_safe' => ['html']]]),
            new TwigFunction('firstHeader', [$this, 'firstHeader', ['is_safe' => ['html']]]),
            new TwigFunction('firstText', [$this, 'firstText', ['is_safe' => ['html']]]),


        ];
    }

    public function ejsrender($json)
    {
        //return $json;
        $html = new \Twig\Markup(Parser::parse($json)->toHtml(), 'UTF-8');
        //ajout des finctionnalitées propre à mickcrud
        //travaille sur les images en ajoutant un filtre liip

        return $html;
    }
    public function firstImage($json)
    {

        $tab = json_decode($json)->blocks;
        foreach ($tab as $key => $value) {
            if ($value->type == 'image') return $value->data->url;
        }
        //return $html;
    }
    public function firstHeader($json)
    {
        $tab = json_decode($json)->blocks;
        foreach ($tab as $key => $value) {
            if ($value->type == 'header') return $value->data->text;
        }
        //return $html;
    }
    public function firstText($json)
    {
        $tab = json_decode($json)->blocks;
        foreach ($tab as $key => $value) {
            if ($value->type == 'paragraph') return $value->data->text;
        }
        //return $html;
    }
}
