<?php

namespace Skygsn\ImageUploader;

class JpegImageResizeHandler implements ImageResizeHandler
{
    /**
     * @param resource $image
     * @param string $fileName
     * @return bool
     */
    public function createNewImage($image, $fileName)
    {
        return imagejpeg($image, $fileName, 75);
    }

    /**
     * @param string $source
     * @return resource
     */
    public function createImageFromSource($source)
    {
        return imagecreatefromjpeg($source);
    }
}
