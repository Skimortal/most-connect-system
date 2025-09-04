<?php
namespace App\EventListener;

use Imagine\Image\Box;
use Imagine\Image\ImagineInterface;
use Vich\UploaderBundle\Event\Event;

class ImageResizeSubscriber
{
    private ImagineInterface $imagine;

    public function __construct(ImagineInterface $imagine)
    {
        $this->imagine = $imagine;
    }

    public function onVichUploaderPostUpload(Event $event): void
    {
        $object = $event->getObject();
        $mapping = $event->getMapping();

        // Prüfe, ob es sich um das Logo-Mapping handelt
        if (
            ($mapping->getMappingName() !== 'company_logo' || !$object->getLogoFile())
        ) {
            return;
        }

        $filePath = $mapping->getUploadDestination() . '/' . $object->getLogoName();
        chmod($filePath, 0664);
        $image = $this->imagine->open($filePath);

        $options = [];

        // Bestimme den Dateityp und die passende Komprimierungsoption
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        if (in_array(strtolower($extension), ['jpeg', 'jpg'])) {
            $options = ['jpeg_quality' => 75];
        } elseif (strtolower($extension) === 'png') {
            $options = ['png_compression_level' => 9];
        }

        if ($image->getSize()->getWidth() > 800 || $image->getSize()->getHeight() > 800) {
            $resizeImage = $image->thumbnail(new Box(500, 500));
            $resizeImage->save($filePath, $options);
        }
    }
}
