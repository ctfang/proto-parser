<?php


namespace ProtoParser\Swagger;


use ProtoParser\FileParser\Message\Message;

class ProtoMessageToSwagger
{
    public static function toResponse(Message $message): Response
    {
        $response = new Response();
        $response->setSchema([
            "\$ref" => "#/definitions/{$message->getName()}"
        ]);
        return $response;
    }

    public static function toDefinition(Message $message): Definition
    {
        $definition = new Definition();
        $definition->setName($message->getName());
        $pars = [];
        foreach ($message->getValues() as $type) {
            if ($type->getBType() == $type::Base) {
                // proto自带类型
                $typeIn = 'integer';
                if (in_array($type->getType(), ['string'])) {
                    $typeIn = 'string';
                }
                $pars[$type->getName()] = [
                    "type"        => $typeIn,
                    "format"      => $type->getType(),
                    "description" => $type->getDoc(),
                ];
            } elseif ($type->getBType() == $type::bTypeObject) {
                // 其他对象引用

                // TODO
            }
        }
        $definition->setProperties($pars);
        return $definition;
    }

    public static function toParameter(Message $message, string $method = 'get'): Parameter
    {
        $parameter = new Parameter();
        foreach ($message->getValues() as $type) {
            if ($type->getBType() == $type::Base) {
                // proto自带类型
                $typeIn = 'integer';
                if (in_array($type->getType(), ['string'])) {
                    $typeIn = 'string';
                }
                $parameter->setName($type->getName());
                $parameter->setDescription($type->getDoc());
                $parameter->setFormat($type->getType());
                $parameter->setType($typeIn);
            } elseif ($type->getBType() == $type::bTypeObject) {
                // 其他对象引用
                $parameter->setName($type->getName());
                $parameter->setDescription($type->getDoc());
                $parameter->setSchema([
                    "\$ref" => "#/definitions/{$type->getType()}"
                ]);
            }
        }

        if ($method == 'get') {
            $parameter->setIn("path");
        } elseif ($method == 'post') {
            $parameter->setIn("body");
        }

        return $parameter;
    }
}