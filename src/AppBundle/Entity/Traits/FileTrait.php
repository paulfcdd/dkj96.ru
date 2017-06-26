<?php

namespace AppBundle\Entity\Traits;

use AppBundle\Entity\File;
use Doctrine\Common\Collections\ArrayCollection;

trait FileTrait
{
    /**
     * @var ArrayCollection
     */
    private $files;

    /**
     * @param File $file
     * @return $this
     */
    public function addFile(File $file)
    {
        if(!$this->files->contains($file)) {
            $this->files->add($file);
        }

        return $this;
    }

    /**
     * Remove $file from collection
     *
     * @param File $file
     * @return $this
     */
    public function removeFile(File $file) {
        if($this->files->contains($file)) {
            $this->files->remove($file);
        }

        return $this;
    }

    /**
     * @param ArrayCollection $collection
     * @return $this
     */
    public function setFiles(ArrayCollection $collection) {
        $this->files = $collection;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getFiles() {
        return $this->files;
    }

    /**
     * @return $this
     */
    public function clearFiles() {
        $this->files->clear();

        return $this;
    }
}