# Twig extensions
clone in in src/Twig 

## Die and Dump
implementation in twig of diedump and dump

{{dd"string array..."}}

## ENV
Return env variable

{{getenv("var")}}
return $_ENV[var]

## VARS
return all variable global

{{dollar($type,$var)}}
$type is: post,get,request,session,server

## Repository
implementation of function repository of symfony for fast developpement

function find, findone, findby et findall
{{findall('page')}}

## Editorjs
functions for editorjs
- ejsrender: render the html of json
- firstImage: return first image of text
- firstHeader: return first header of text
- firstText: return first paragraph of text
