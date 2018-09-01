<?php

namespace Skygsn\ImageUploader;

class ImageUploader
{
    const FILE_TYPE_ERROR = 0;
    const FILE_SIZE_ERROR = 1;
    const CREATE_DIRECTORY_ERROR = 2;
    const UPLOAD_IMAGE_ERROR = 3;

    /**
     * @var ImageResizer
     */
    private $resizer;

    /**
     * @var string
     */
    private $moduleName;

    /**
     * @var ImageSize[]
     */
    private $targetDimensions = [];

    /**
     * @var array
     */
    private static $imageMimeTypes = [
        'image/jpeg',
        'image/pjpeg',
        'image/png'
    ];

    /**
     * @var string[]
     */
    private $resultImages = [];

    /**
     * @param string $moduleName
     * @param array $targetDimensions
     * @param ImageResizer $resizer
     */
    public function __construct($moduleName, array $targetDimensions, ImageResizer $resizer)
    {
        $this->moduleName = $moduleName;
        $this->targetDimensions = $targetDimensions;
        $this->resizer = $resizer;
    }

    /**
     * @param string $tmpFile
     * @return string[]
     * @throws \Exception
     */
    public function upload($tmpFile)
    {
        $fileMimeType = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $tmpFile);

        if (!in_array($fileMimeType, self::$imageMimeTypes)) {
            throw new ImageWasNotUploaded(sprintf(
                'Некорректный тип файла %s',
                $fileMimeType
            ), self::FILE_TYPE_ERROR);
        }

        $originalFileName = $this->createFileName($fileMimeType, $tmpFile);

        $firstDir = substr($originalFileName, 0, 2);
        $secondDir = substr($originalFileName, 2, 2);

        $fileDestination = 'uploads/' . $this->moduleName . '/original/' . $firstDir . '/' . $secondDir . '/';

        if (!mkdir($fileDestination, 0777, true)) {
            throw new ImageWasNotUploaded(sprintf(
                'Не удалось создать директорию %s',
                $fileDestination
            ), self::CREATE_DIRECTORY_ERROR);
        }

        $originalFile = $fileDestination . $originalFileName;

        if (!copy($_FILES['files']['tmp_name'][0], $originalFile)) {
            throw new ImageWasNotUploaded(sprintf(
                'Не удалось загрузить %s',
                $originalFile
            ), self::UPLOAD_IMAGE_ERROR);
        }

        unlink($_FILES['files']['tmp_name'][0]);

        $IM = new \Imagick($originalFile);
        $orientation = $IM->getImageOrientation();
        switch($orientation) {
            case \Imagick::ORIENTATION_BOTTOMRIGHT:
                $IM->rotateImage("#000", 180); // rotate 180 degrees
                break;
            case \Imagick::ORIENTATION_RIGHTTOP:
                $IM->rotateImage("#000", 90); // rotate 90 degrees CW
                break;
            case \Imagick::ORIENTATION_LEFTBOTTOM:
                $IM->rotateImage("#000", -90); // rotate 90 degrees CCW
                break;
        }
        $IM->setImageOrientation(\Imagick::ORIENTATION_TOPLEFT);
        $IM->writeImage($originalFile);
        $IM->clear();
        $IM->destroy();

        $this->resultImages[] = $originalFile;

        foreach ($this->targetDimensions as $targetDimension) {
            $destination = sprintf(
                'uploads/%s/%s/%s/%s/',
                $this->moduleName,
                $targetDimension->getDirectoryName(),
                $firstDir,
                $secondDir
            );

            if (!mkdir($destination, 0777, true)) {
                throw new ImageWasNotUploaded(sprintf(
                    'Не удалось создать директорию %s',
                    $destination
                ), self::CREATE_DIRECTORY_ERROR);
            }

            if ($this->resizer->resize(
                new Image($originalFile, $originalFileName, $fileMimeType),
                $destination,
                $targetDimension
            )) {
                $this->resultImages[] = $destination . $originalFileName;
            }
        }

        return $this->resultImages;
    }

    /**
     * @param string $fileMimeType
     * @param string $tmpFile
     * @return string
     */
    private function createFileName($fileMimeType, $tmpFile)
    {
        $extension = '.jpg';
        if ($fileMimeType == 'image/png') {
            $extension = '.png';
        }

        return md5(md5_file($tmpFile) . time() . mt_rand(1, 1000)) . $extension;
    }
}
