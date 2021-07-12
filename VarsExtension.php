<?php

namespace App\CMTwig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class VarsExtension extends AbstractExtension
{

    public function getFunctions(): array
    {
        return [
            new TwigFunction('ini_get', [$this, 'ini_get']),
            new TwigFunction('dollar', [$this, 'dollar']),
        ];
    }

    public function getFilters()
    {
        return [
            new TwigFilter('return_bytes', [$this, 'return_bytes']),
            new TwigFilter('jsonDecode', [$this, 'jsonDecode']),
        ];
    }
    public function jsonDecode($str)
    {
        $tab = json_decode($str, true);
        return (json_encode(json_decode($str), JSON_PRETTY_PRINT));
    }
    public function ini_get($value)
    {
        return ini_get($value);
    }
    function return_bytes($val)
    {
        $val = trim($val);
        $last = strtolower($val[strlen($val) - 1]);
        $int = substr($val, 0, -strlen($last));
        switch ($last) {
            case 'g':
                $int *= 1024;
            case 'm':
                $int *= 1024;
            case 'k':
                $int *= 1024;
        }

        return $int;
    }
    /***************************************************************************************************
     *                                       GET POST SESSION SERVER ET REQUEST                                       *
     ***************************************************************************************************/
    /**
     * permet d'utiliser les $_GET, $_POST, $_SESSION, $_SERVER et $_REQUEST dans les twigs
     * @param string $type post get request session ou server
     * @param string $var variable
     * @return string
     */
    function dollar($type, $var)
    {
        switch ($type) {
            case 'post':
                return $_POST[$var];
                break;
            case 'get':
                return $_GET[$var];
                break;
            case 'request':
                return $_REQUEST[$var];
                break;
            case 'session':
                return $_SESSION[$var];
                break;
            case 'server':
                return $_SERVER[$var];
                break;
        }
    }
}
