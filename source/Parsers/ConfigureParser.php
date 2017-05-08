<?php
/**
 * Created by Fernando Gonçalves (http://batutinha.github.io/)
 * User: batutinha
 * Date: 07/05/17
 * Time: 06:31
 */

namespace PagSeguro\Parsers;


trait ConfigureParser
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

    private static function combineConfig(array $data = []): array
    {
        if(empty($data)){
            throw new \InvalidArgumentException('Config for merge is empty');
        }

        $diffKeys = array_keys(array_diff_key($data, self::$defaultConf));
        if(!empty($diffKeys)){
            foreach ($diffKeys as $key){
                unset($data[$key]);
            }
        }

        self::$defaultConf = array_replace_recursive(self::$defaultConf, $data);
        self::$configGenerated = true;
        return self::$defaultConf;
    }
}