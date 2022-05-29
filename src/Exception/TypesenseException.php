<?php

namespace App\Exception;

use RuntimeException;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class TypesenseException extends RuntimeException
{
    public $status;

    /**
     * @var string
     */
    public $message;

    /**
     * TypesenseException constructor.
     * @param ResponseInterface $response
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function __construct(ResponseInterface $response)
    {
        parent::__construct(json_decode($response->getContent(false), true)['message'] ?? '');

        $this->status = $response->getStatusCode();
        $this->message = json_decode($response->getContent(false), true)['message'] ?? '';
    }
}
