<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use App\Repository\CategoryRepository;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 * @ORM\Table("blog_category")
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private ?int $id = null; 

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name = '';

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"name"}, unique=true)
     *
     * @ORM\Column(type="string", length=100)
     */
    private string $slug;

    /**
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    private int $postsCount = 0;

    /**
     * @var Collection<int,Post>
     *
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="category")
     */
    private Collection $posts;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Category
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): Category
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPostsCount(): ?int
    {
        return $this->postsCount;
    }

    public function setPostsCount(int $postsCount): Category
    {
        $this->postsCount = $postsCount;

        return $this;
    }

    /**
     * @return Collection<int,Post>|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setCategory($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            // set the owning side to null (unless already changed)
            if ($post->getCategory() === $this) {
                $post->setCategory(null);
            }
        }

        return $this;
    }
}
