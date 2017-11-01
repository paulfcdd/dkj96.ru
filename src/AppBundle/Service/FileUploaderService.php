<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploaderService
{

    const AVAILABLE_EXT = [
        'image/jpeg',
        'image/png',
        'video/mp4',
        'video/mpeg'
    ];

    const IMAGES = [
        'jpg',
        'jpeg',
        'png',
    ];

    const VIDEOS = [
        'mp4',
        'mpeg'
    ];

    /** @var EntityManager $em */
    protected $em;

    /** @var  UploadedFile $file */
    protected $file;

    /** @var  string $dir */
    protected $dir;

    /** @var ContainerInterface $container */
    protected $container;

    /** @var  string $mimeType */
    protected $mimeType;

    /**
     * ImageUploader constructor.
     * @param EntityManager $entityManager
     * @param ContainerInterface $container
     */
    public function __construct(EntityManager $entityManager, ContainerInterface $container)
    {
        $this->em = $entityManager;

        $this->container = $container;
    }

    /**
     * @return string
     */
    public function getMimeType() {
        return $this->getFile()->getClientOriginalExtension();
    }

    /**
     * @param UploadedFile $file
     * @return $this
     */
    public function setFile(UploadedFile $file) {
        $this->file = $file;

        return $this;
    }

    /**
     * @return UploadedFile
     */
    public function getFile(){
        return $this->file;
    }

    /**
     * @param string $dir
     * @return $this
     */
    public function setDir(string $dir) {
        $this->dir = $dir;

        return $this;
    }

    /**
     * @return string
     */
    public function getDir() {
        return $this->dir;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function upload() {

        $fileExt = $this->getFile()->getMimeType();

        if (!in_array($fileExt, self::AVAILABLE_EXT)) {
            throw new \Exception('Файлы с расширением \'.'.$fileExt.'\' не поддерживаются');
        }

        $uploadDir = $this->checkUploadDir($this->getDir());

        $fileNameHash = uniqid();

        if (!$uploadDir) {
            die($uploadDir);
        } else {
            $fileName = $fileNameHash.'.'.strtolower($this->getFile()->getClientOriginalExtension());

            $this->getFile()->move($uploadDir, $fileName);

            return $fileName;
        }

    }

    /**
     * @param $uploadDir
     * @return bool|null|string
     */
    private function checkUploadDir($uploadDir) {

        $path = $this->container->getParameter('upload_directory').'/'.$uploadDir;

        if (!is_dir($path)) {
            try {
                mkdir($path);
                return $path;
            } catch (\Exception $exception) {
                return $exception->getMessage();
            }
        } else {
            return $path;
        }
    }
}