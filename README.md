# Twig extensions
clone in in src/Twig 

## Die and Dump
{{dd"string array..."}}

## ENV
{{getenv("var")}}
return $_ENV[var]

## VARS
{{dollar($type,$var)}}
$type is: post,get,request,session,server

## Repository
function find, findone, findby et findall
{{findall('page')}}