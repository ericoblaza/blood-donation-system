<?php

declare(strict_types=1);

namespace Core\Http;

//handles what the server sends back to the browser

class Response
{
    private int $statusCode = 200;

    /** @var array<string, string> */
    private array $headers = [];

    public function setStatusCode(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    public function setHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * Send headers + status, then output body.
     * Call once per request (after this, avoid changing headers).
     */
    public function send(string $content): void
    {
        if (!headers_sent()) {
            http_response_code($this->statusCode);
            foreach ($this->headers as $name => $value) {
                header("{$name}: {$value}");
            }
        }

        echo $content;
    }

    /** Convenience for redirects (302 by default). */
    public function redirect(string $url, int $statusCode = 302): void
    {
        $this->setStatusCode($statusCode)->setHeader('Location', $url)->send('');
    }
}

