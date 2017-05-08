<?php
/**
 * Created by Fernando Gonçalves (http://batutinha.github.io/)
 * User: batutinha
 * Date: 07/05/17
 * Time: 06:31
 */

namespace PagSeguro\Parsers;


trait XmlToArray
{
    private static function xmlToArray(\SimpleXMLElement $element): array
    {
        if($element instanceof \SimpleXMLElement){
            $element = (array) $element;
        }

        foreach ($element as $key => $value){
            if($key === 'comment'){
                unset($element[$key]);
                continue;
            }
            if($value instanceof \SimpleXMLElement){
                $element[$key] = self::xmlToArray($value);
            }
        }

        return $element;
    }
}