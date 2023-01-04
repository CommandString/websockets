<?php

namespace Routes\Files;

use HttpSoft\Message\Response;
use HttpSoft\Response\EmptyResponse;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use stdClass;

class Files {
    private static stdClass $mimes;

    public static function main(RequestInterface $req, ResponseInterface $res): ResponseInterface
    {
        $unreal_file_path = "./public" . str_replace("../", "", $req->getRequestTarget());

        if (!file_exists($unreal_file_path)) {
            return new EmptyResponse(404);
        }

        $path_to_file = realpath($unreal_file_path);

        $file_contents = file_get_contents($path_to_file);
        $file_ext = str_replace(".", "", strchr($req->getRequestTarget(), "."));

        return self::createResponseFromFile($file_contents, $file_ext);
    }

    public static function createResponseFromFile(string $contents, string $extension) {
        $res = (new Response())->withHeader("Content-Type", self::getMimeFromExtension($extension))->withHeader("Content-Length", strlen($contents));
        $res->getBody()->write($contents);

        return $res;
    }

    public static function getMimes(): stdClass
    {
        if (!isset(self::$mimes)) {
            self::$mimes = json_decode(file_get_contents(__DIR__ . "/Mimes.json"));
        }

        return self::$mimes;
    }

    public static function getMimeFromExtension(string $extensionToFindMimeFor): ?string
    {
        foreach (self::getMimes() as $mime => $extensions) {
            foreach ($extensions as $extension) {
                if ($extension == $extensionToFindMimeFor) {
                    return $mime;
                }
            }
        }

        return null;
    }

    public static function generateRegex(): string
    {
        $regex = "";

        foreach (self::getMimes() as $extensions) {
            foreach ($extensions as $extension) {
                $regex .= sprintf(".%s|", $extension);
            }
        }
        
        return substr($regex, 0, -1);
    }
}