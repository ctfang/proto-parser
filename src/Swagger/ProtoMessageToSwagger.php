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
        $response->setDescription($message->getDoc());
        return $response;
    }

    public static function toDefinition(Message $message): Definition
    {
        $definition = new Definition();
        $definition->setName($message->getName());
        $pars = [];
        foreach ($message->getValues() as $type) {
            if ($type->getBType() == $type::Base) {
                $pars[$type->getName()] = [
                    "type"        => self::toSwaggerType($type->getType()),
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

    public static function toParameter(Message $message, string $method = 'get')
    {
        if ($method == 'get') {
            $got = [];
            // 写到url上
            foreach ($message->getValues() as $type) {
                $parameter = new Parameter();
                // 其他对象引用
                $parameter->setName($message->getName());
                $parameter->setDescription($message->getDoc());
                if ($type->getBType() == $type::Base) {
                    // proto自带类型
                    $parameter->setName($type->getName());
                    $parameter->setDescription($type->getDoc());
                    $parameter->setFormat($type->getType());
                    $parameter->setType(self::toSwaggerType($type->getType()));
                } elseif ($type->getBType() == $type::bTypeObject) {
                    // 其他对象引用
                    $parameter->setName($type->getName());
                    $parameter->setDescription($type->getDoc());
                    $parameter->setSchema([
                        "\$ref" => "#/definitions/{$type->getType()}"
                    ]);
                }
                $parameter->setIn("query");
                $got[] = $parameter;
            }
            return $got;
        } elseif ($method == 'post') {
            $parameter = new Parameter();
            // 其他对象引用
            $parameter->setName($message->getName());
            $parameter->setDescription($message->getDoc());
            $parameter->setSchema([
                "\$ref" => "#/definitions/{$message->getName()}"
            ]);
            $parameter->setIn("body");
            return [$parameter];
        }
        return [];
    }

    // proto自带类型 转 swagger
    public static function toSwaggerType(string $source): string
    {
        if (in_array($source, ['string', 'bytes'])) {
            $typeIn = 'string';
        } elseif (in_array($source, ['bool'])) {
            $typeIn = 'boolean';
        } else {
            $typeIn = 'integer';
        }
        return $typeIn;
    }
}