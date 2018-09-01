<?php

namespace Skygsn\ImageUploader;

class Image
{
    /**
     * @var string
     */
    private $source;

    /**
     * @var string
     */
    private $mimeType;

    /**
     * @var string
     */
    private $fileName;

    /**
     * @param string $source
     * @param string $fileName
     * @param string $mimeType
     */
    public function __construct($source, $fileName, $mimeType)
    {
        $this->source = $source;
        $this->mimeType = $mimeType;
        $this->fileName = $fileName;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }
}
