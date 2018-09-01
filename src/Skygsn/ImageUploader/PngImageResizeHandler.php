<?php

namespace Skygsn\ImageUploader;

class PngImageResizeHandler implements ImageResizeHandler
{
    /**
     * @param resource $image
     * @param string $fileName
     * @return bool
     */
    public function createNewImage($image, $fileName)
    {
        return imagepng($image, $fileName, 9);
    }

    /**
     * @param string $source
     * @return resource
     */
    public function createImageFromSource($source)
    {
        return imagecreatefrompng($source);
    }
}
