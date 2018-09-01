<?php

namespace Skygsn\ImageUploader;

interface ImageResizeHandler
{
    /**
     * @param resource $image
     * @param string $fileName
     * @return bool
     */
    public function createNewImage($image, $fileName);

    /**
     * @param string $source
     * @return resource
     */
    public function createImageFromSource($source);
}
