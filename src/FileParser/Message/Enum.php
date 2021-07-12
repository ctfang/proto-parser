<?php


namespace ProtoParser\FileParser\Message;


use ProtoParser\ProtoToArray;
use ProtoParser\StringHelp;

class Enum
{
    protected $values = [];

    public function __construct(array $source)
    {
        $array      = array_values($source['code']);
        $this->doc  = $source['doc'];
        $this->name = $array[2];

        $offset = 0;
        for (; $offset < count($array); $offset++) {
            if ($array[$offset] == "{") {
                $offset++;
                break;
            }
        }

        $arr  = [];
        $doc  = '';
        $init = 0;
        for (; $offset < count($array); $offset++) {
            $str = $array[$offset];
            if ($init == 0) {
                if (!in_array($str, ProtoToArray::Separator)) {
                    $init++;
                } else {
                    continue;
                }
            }

            if (isset($str[0]) && $str[0] == '/') {
                $doc = $str;
            } elseif ($str != PHP_EOL) {
                $arr[] = $str;
                if ($str == ";") {
                    $this->values[] = [
                        'doc'  => $doc,
                        'name' => $arr[0],
                        'num'  => $arr[3],
                    ];
                    $doc            = '';
                    $arr            = [];
                }
            }
        }
    }

    public function getName(): string
    {
        return $this->name;
    }
}