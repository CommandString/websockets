<?php

namespace Routes\Files;

use React\Http\Message\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use React\EventLoop\Loop;
use React\EventLoop\Timer\Timer;
use React\Stream\ThroughStream;
use stdClass;

class Files {
    private static stdClass $mimes;

    public static function main(RequestInterface $req, ResponseInterface $res): ResponseInterface
    {
        $unreal_file_path = "./public" . str_replace("../", "", $req->getRequestTarget());

        if (!file_exists($unreal_file_path)) {
            return \Routes\ErrorHandler::handle404($req);
        }

        $path_to_file = realpath($unreal_file_path);

        $file_stream = fopen($path_to_file, "r");
        $stream = new ThroughStream();
        $file_ext = str_replace(".", "", strchr($req->getRequestTarget(), "."));
        
        rewind($file_stream);

        Loop::addPeriodicTimer(1e-6, function (Timer $timer) use ($stream, $file_stream) {
            $stream->write(fgets($file_stream));

            if (feof($file_stream)) {
                $stream->end();
                Loop::cancelTimer($timer);
            }
        });

        return new Response(
            Response::STATUS_OK,
            array(
                'Content-Type' => self::getMimeFromExtension($file_ext)
            ),
            $stream
        );
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
