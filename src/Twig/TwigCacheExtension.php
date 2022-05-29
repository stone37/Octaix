<?php

namespace App\Twig;

use App\Twig\CacheExtension\CacheableInterface;
use App\Twig\CacheExtension\CacheTokenParser;
use App\Orm\IterableQueryBuilder;
use DateTimeImmutable;
use Error;
use Exception;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Twig\Extension\AbstractExtension;
use Twig\TokenParser\AbstractTokenParser;

class TwigCacheExtension extends AbstractExtension
{
    /**
     * @var bool
     */
    private $active;

    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    public function __construct(CacheItemPoolInterface $viewCachePool, bool $active = true)
    {
        $this->cache = $viewCachePool;
        $this->active = $active;
    }

    /**
     * @return array<AbstractTokenParser>
     */
    public function getTokenParsers(): array
    {
        return [
            new CacheTokenParser(),
        ];
    }

    /**
     * @param string $templatePath
     * @param CacheableInterface|string|array|bool|null $item
     * @param bool $prefix
     * @return string
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws Exception
     */
    public function getCacheKey(string $templatePath, $item, bool $prefix = true): string
    {
        if (true === $prefix) {
            $prefix = (new AsciiSlugger())->slug(str_replace('.html.twig', '', $templatePath)).'_';
        } else {
            $prefix = '';
        }

        if (is_bool($item)) {
            return $prefix.($item ? '1' : '0');
        }

        if (empty($item)) {
            throw new Exception('Clef de cache invalide');
        }

        if (is_string($item)) {
            return $prefix.$item;
        }

        if (is_array($item)) {
            return $prefix.implode('_', array_map(function ($v, $templatePath) {$this->getCacheKey($templatePath, $v, false);}, $item));
        }

        if ($item instanceof IterableQueryBuilder) {
            $item = $item->getFirstResultOnly();
            if (null === $item) {
                return $prefix.'noresult';
            }
        }

        if (!is_object($item) || !($item instanceof CacheableInterface)) {
            throw new Exception("TwigCache : Impossible de serialiser une variable qui n'est pas un objet ou une chaine");
        }

        try {
            $updatedAt = $item->getUpdatedAt() ?: new DateTimeImmutable('@0');
            $id = $item->getId() ?: '0';
            $className = get_class($item);
            $className = substr($className, strrpos($className, '\\') + 1);

            return $prefix.$id.$className.$updatedAt->getTimestamp();
        } catch (Error $e) {
            throw new Exception("TwigCache : Impossible de serialiser l'objet pour le cache : \n".$e->getMessage());
        }
    }

    /**
     * @param CacheableInterface|string $item
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCacheValue(string $templatePath, $item): ?string
    {
        if (!$this->active) {
            return null;
        }
        $item = $this->cache->getItem($this->getCacheKey($templatePath, $item));

        return $item->get();
    }

    /**
     * @param CacheableInterface|string $item
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function setCacheValue(string $templatePath, $item, string $value): void
    {
        if (!$this->active) {
            return;
        }
        $item = $this->cache->getItem($this->getCacheKey($templatePath, $item));
        $item->set($value);
        $this->cache->save($item);
    }
}
