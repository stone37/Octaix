<?php

namespace App\Orm;

use ArrayAccess;
use ArrayIterator;
use Doctrine\ORM\QueryBuilder;
use IteratorAggregate;

/**
 * Rend une requête iterable.
 *
 * Cette classe permet de passer des requêtes au template sans les éxécuter en amont pour améliorer
 * l'efficacité du cache.
 * La requête n'est pas éxécuté avant la première itération
 *
 * @template E of object
 */
class IterableQueryBuilder extends QueryBuilder implements IteratorAggregate, ArrayAccess
{
    private $firstResultFetched = false;
    private $firstResult = null;
    private $results = null;

    /**
     * This will extract the first result from the query (without collecting the other elements).
     *
     * @return object|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getFirstResultOnly(): ?object
    {
        if (false === $this->firstResultFetched) {
            $this->firstResultFetched = true;
            $this->firstResult = $this->getQuery()->setMaxResults(1)->getOneOrNullResult();
        }

        return $this->firstResult;
    }

    /**
     * @return array
     */
    public function getResults(): array
    {
        if (null === $this->results) {
            $this->results = $this->getQuery()->getResult();
        }

        return $this->results;
    }

    /**
     * @return ArrayAccess
     */
    public function getIterator(): ArrayAccess
    {
        if (null === $this->results) {
            $this->results = $this->getQuery()->getResult();
        }

        return new ArrayIterator($this->results);
    }

    /**
     * @param string $offset
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->getResults());
    }

    /**
     * @param string $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getResults()[$offset];
    }

    /**
     * @param string $offset
     * @param mixed  $value
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->getResults()[$offset] = $value;
    }

    /**
     * @param string $offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->getResults()[$offset]);
    }
}
