<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

final class FpmStatus
{
    public function read(string $host, int $port): array
    {
        $socket = @stream_socket_client("tcp://{$host}:{$port}", $errno, $error, 2);

        if ($socket === false) {
            throw new RuntimeException('FPM status listener unavailable');
        }

        stream_set_timeout($socket, 2);

        try {
            $params = '';

            foreach (['REQUEST_METHOD' => 'GET', 'SCRIPT_NAME' => '/status', 'SCRIPT_FILENAME' => '/status', 'QUERY_STRING' => 'json', 'SERVER_PROTOCOL' => 'HTTP/1.1'] as $key => $value) {
                $params .= \chr(\strlen($key)).\chr(\strlen($value)).$key.$value;
            }

            $request = $this->record(1, pack('nCxxxxx', 1, 0)).$this->record(4, $params).$this->record(4, '').$this->record(5, '');
            $offset = 0;

            while ($offset < \strlen($request)) {
                $written = fwrite($socket, substr($request, $offset));

                if ($written === false || $written === 0) {
                    throw new RuntimeException('FPM status write failed');
                }
                $offset += $written;
            }

            $stdout = '';

            do {
                $header = unpack('Cversion/Ctype/nid/nlength/Cpadding/Creserved', $this->bytes($socket, 8));
                $body = $this->bytes($socket, $header['length']);
                $this->bytes($socket, $header['padding']);

                if ($header['type'] === 6) {
                    $stdout .= $body;
                }
            } while ($header['type'] !== 3);

            $separator = strpos($stdout, "\r\n\r\n");

            if ($separator === false) {
                throw new RuntimeException('Invalid FPM status response');
            }

            return json_decode(substr($stdout, $separator + 4), true, flags: JSON_THROW_ON_ERROR);
        } finally {
            fclose($socket);
        }
    }

    private function record(int $type, string $body): string
    {
        return pack('CCnnCC', 1, $type, 1, \strlen($body), 0, 0).$body;
    }

    private function bytes($socket, int $length): string
    {
        $result = '';

        while (\strlen($result) < $length) {
            $chunk = fread($socket, $length - \strlen($result));

            if ($chunk === false || $chunk === '') {
                throw new RuntimeException('FPM status read failed or timed out');
            }
            $result .= $chunk;
        }

        return $result;
    }
}
