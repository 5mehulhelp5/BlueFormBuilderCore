<?php
/**
 * Cytracon
 *
 * This source file is subject to the Cytracon Software License, which is available at https://www.cytracon.com/license.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.cytracon.com for more information.
 *
 * @category  BlueFormBuilder
 * @package   Cytracon_BlueFormBuilderCore
 * @copyright Copyright (C) 2019 Cytracon (https://www.cytracon.com)
 */

namespace Cytracon\BlueFormBuilderCore\Cron;

use \Magento\Framework\App\Filesystem\DirectoryList;

class CleanFiles
{
    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $mediaDirectory;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Cytracon\BlueFormBuilderCore\Model\ResourceModel\File\CollectionFactory
     */
    protected $fileCollectionFactory;

    /**
     * @param \Magento\Framework\Filesystem                                    $filesystem
     * @param \Magento\Framework\ObjectManagerInterface                        $objectManager
     * @param \Cytracon\BlueFormBuilderCore\Model\ResourceModel\File\CollectionFactory $fileCollectionFactory
     */
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Cytracon\BlueFormBuilderCore\Model\ResourceModel\File\CollectionFactory $fileCollectionFactory
    ) {
        $this->mediaDirectory        = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->_objectManager        = $objectManager;
        $this->fileCollectionFactory = $fileCollectionFactory;
    }

    /**
     * Delete un-usued attachments
     *
     * @return void
     */
    public function execute()
    {
        $fileCollection = $this->fileCollectionFactory->create();

        $baseMediaPath = \Cytracon\BlueFormBuilderCore\Model\File::UPLOAD_FOLDER;
        $this->_deleteFiles($fileCollection, $baseMediaPath);
        $this->_deleteDirectories($baseMediaPath);
    }

    protected function _deleteFiles($fileCollection, $baseMediaPath)
    {
        $dirAbsPath     = $this->mediaDirectory->getAbsolutePath($baseMediaPath);
        $collection     = $this->_objectManager->create(\Magento\Framework\Data\Collection\Filesystem::class);
        $collection->addTargetDir($dirAbsPath);

        foreach ($collection as $item) {
            $fileName = str_replace($dirAbsPath, '', $item->getFilename());
            $status   = false;
            foreach ($fileCollection as $_file) {
                if ($_file->getFile() == $fileName) {
                    $status = true;
                }
            }
            if (!$status) {
                $path = $item->getFilename();
                if (file_exists($path) && is_writable($path)) {
                    unlink($path);
                }
            }
        }
    }

    protected function _deleteDirectories($baseMediaPath)
    {
        $this->mediaDirectory->delete(\Cytracon\BlueFormBuilderCore\Model\File::UPLOAD_TMP);
        $dirAbsPath = $this->mediaDirectory->getAbsolutePath($baseMediaPath);
        $collection = $this->_objectManager->create(\Magento\Framework\Data\Collection\Filesystem::class);
        $collection->addTargetDir($dirAbsPath)
        ->setCollectDirs(true)
        ->setCollectFiles(false)
        ->setCollectRecursively(true);
        $items = $collection->getItems();
        krsort($items);

        foreach ($items as $dir) {
            if ($this->isDirEmpty($dir->getFilename())) {
                $fileName = str_replace($dirAbsPath, $baseMediaPath, $dir->getFilename());
                $this->mediaDirectory->delete($fileName);
            }
        }
    }

    /**
     * @param  string  $dir
     * @return boolean
     */
    private function isDirEmpty($dir)
    {
        if (!is_readable($dir)) {
            return null;
        }
        $handle = opendir($dir);
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != ".." && $entry != ".DS_Store") {
                return false;
            }
        }
        return true;
    }
}
