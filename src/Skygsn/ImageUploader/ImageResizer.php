<?php

namespace Skygsn\ImageUploader;

class ImageResizer
{
    /**
     * @var ImageResizerHandlerFactory
     */
    private $handlerFactory;

    /**
     * @param ImageResizerHandlerFactory $handlerFactory
     */
    public function __construct(ImageResizerHandlerFactory $handlerFactory)
    {
        $this->handlerFactory = $handlerFactory;
    }

    /**
     * @param Image $sourceImage
     * @param $targetImagePath
     * @param ImageSize $size
     * @return bool
     */
    public function resize(Image $sourceImage, $targetImagePath, ImageSize $size)
    {
        $imageHandler = $this->handlerFactory->createFromMimeType($sourceImage->getMimeType());

        $image = $imageHandler->createImageFromSource($sourceImage->getSource());
        $img_width = imagesx($image);
        $img_height = imagesy($image);

        $scale = min(
            $size->getWidth() / $img_width,
            $size->getHeight() / $img_height
        );

        $new_width = $img_width * $scale;
        $new_height = $img_height * $scale;
        $dst_x = 0;
        $dst_y = 0;
        $new_img = imagecreatetruecolor($new_width, $new_height);

        return imagecopyresampled(
            $new_img,
            $image,
            $dst_x,
            $dst_y,
            0,
            0,
            $new_width,
            $new_height,
            $img_width,
            $img_height
        ) &&
        $imageHandler->createNewImage($new_img, $targetImagePath . $sourceImage->getFileName());
    }
}
