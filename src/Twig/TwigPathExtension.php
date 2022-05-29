<?php

namespace App\Twig;

use App\Entity\AdvertPicture;
use App\Service\ImageResizer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class TwigPathExtension extends AbstractExtension
{
    private $imageResizer;
    private $helper;

    public function __construct(
        ImageResizer $imageResizer,
        UploaderHelper $helper
    ) {
        $this->imageResizer = $imageResizer;
        $this->helper = $helper;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('uploads_path', [$this, 'uploadsPath']),
            new TwigFunction('image_url', [$this, 'imageUrl']),
            new TwigFunction('image_ad_url', [$this, 'imageAdUrl']),
            new TwigFunction('image_url_raw', [$this, 'imageUrlRaw']),
            new TwigFunction('image', [$this, 'imageTag'], ['is_safe' => ['html']]),
        ];
    }

    public function uploadsPath(string $path): string
    {
        return '/uploads/'.trim($path, '/');
    }

    public function imageUrl(?object $entity, ?int $width = null, ?int $height = null): ?string
    {
        if (null === $entity) {
            return null;
        }

        $path = $this->helper->asset($entity);

        if (null === $path) {
            return null;
        }

        //dump($path);

        if ('jpg' !== pathinfo($path, PATHINFO_EXTENSION)) {
            return $path;
        }

        //dump($this->imageResizer->resize($this->helper->asset($entity), $width, $height));

        return $this->imageResizer->resize($this->helper->asset($entity), $width, $height);
    }

    public function imageUrlRaw(?object $entity): string
    {
        if (null === $entity) {
            return '';
        }

        return $this->helper->asset($entity) ?: '';
    }

    public function imageTag(?object $entity, ?int $width = null, ?int $height = null): ?string
    {
        $url = $this->imageUrl($entity, $width, $height);
        if (null !== $url) {
            return "<img src=\"{$url}\" width=\"{$width}\" height=\"{$height}\"/>";
        }

        return null;
    }

    public function imageAdUrl(?AdvertPicture $picture, ?int $width = null, ?int $height = null): ?string
    {
        if (null === $picture) {
            return null;
        }

        $path = $picture->getWebPath();

        if (null === $path) {
            return null;
        }

        if ('jpg' !== pathinfo($path, PATHINFO_EXTENSION)) {
            return $path;
        }

        return $this->imageResizer->resize($path, $width, $height);
    }
}
