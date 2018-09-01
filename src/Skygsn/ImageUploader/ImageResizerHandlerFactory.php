<?php

namespace Skygsn\ImageUploader;

class ImageResizerHandlerFactory
{
    /**
     * @param string $mimeType
     * @return ImageResizeHandler
     * @throws \Exception
     */
    public function createFromMimeType($mimeType)
    {
        if (in_array($mimeType, ['image/jpeg', 'image/pjpeg'])) {
            return new JpegImageResizeHandler();
        }

        if ($mimeType == 'image/png') {
            return new PngImageResizeHandler();
        }

        throw new \Exception(sprintf('Обработчик для типа %s не найден', $mimeType));
    }
}
