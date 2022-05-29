<?php

namespace App\Entity\Traits;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * Trait TreeTrait
 * @package App\Entity\Traits
 */
trait TreeTrait
{
    /**
     * @var int
     *
     * @Gedmo\TreeRoot()
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rootNode;

    /**
     * @var integer
     *
     * @Gedmo\TreeLeft()
     *
     * @ORM\Column(type="integer")
     */
    private $leftNode;

    /**
     * @var int
     *
     * @Gedmo\TreeRight()
     *
     * @ORM\Column(type="integer")
     */
    private $rightNode;

    /**
     * @var int
     *
     * @Gedmo\TreeLevel()
     *
     * @ORM\Column(type="integer")
     */
    private $levelDepth;

    /**
     * @return int
     */
    public function getRootNode(): ?int
    {
        return $this->rootNode;
    }

    /**
     * @param int $rootNode
     */
    public function setRootNode(?int $rootNode): self
    {
        $this->rootNode = $rootNode;

        return $this;
    }

    /**
     * @return int
     */
    public function getLeftNode(): ?int
    {
        return $this->leftNode;
    }

    /**
     * @param int $leftNode
     */
    public function setLeftNode(?int $leftNode): self
    {
        $this->leftNode = $leftNode;

        return $this;
    }

    /**
     * @return int
     */
    public function getRightNode(): ?int
    {
        return $this->rightNode;
    }

    /**
     * @param int $rightNode
     */
    public function setRightNode(?int $rightNode): self
    {
        $this->rightNode = $rightNode;

        return $this;
    }

    /**
     * @return int
     */
    public function getLevelDepth(): ?int
    {
        return $this->levelDepth;
    }

    /**
     * @param int $levelDepth
     */
    public function setLevelDepth(?int $levelDepth): self
    {
        $this->levelDepth = $levelDepth;

        return $this;
    }
}


