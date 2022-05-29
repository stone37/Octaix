<?php

namespace App\Encoder;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;

/**
 * Convertit un tableau (issue du pathNormalizer) en chemin.
 *
 * ## Exemple
 *
 * [
 *      'path' => 'blog_show',
 *      'params' => ['slug' => 'demo', 'id' => 3],
 *      'hash' => 'c3'
 * ]
 */
class PathEncoder implements EncoderInterface
{
    const FORMAT = 'path';

    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param array $data
     */
    public function encode($data, string $format, array $context = []): string
    {
        ['path' => $path, 'params' => $params] = array_merge(['params' => []], $data);

        $hash = isset($data['hash']) ? '#'.$data['hash'] : '';
        $url = $context['url'] ?? false;

        return $this->urlGenerator->generate($path, $params, $url ? UrlGeneratorInterface::ABSOLUTE_URL : UrlGeneratorInterface::ABSOLUTE_PATH).$hash;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsEncoding(string $format): bool
    {
        return self::FORMAT === $format;
    }
}
