<?php

namespace App\Listener;

use App\Entity\Picture;
use Vich\UploaderBundle\Event\Event;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class ImageUploadListener
{

    private CacheManager $cacheManager;
    private UploaderHelper $uploaderHelper;

    public function __construct(CacheManager $cacheManager, UploaderHelper $uploaderHelper)
    {
        $this->cacheManager = $cacheManager;
        $this->uploaderHelper = $uploaderHelper;
    }

    public function onVichUploaderPreRemove(Event $event): void
    {
        $entity = $event->getObject();

        if (!$entity instanceof Picture) {
            return;
        }

        $this->cacheManager->remove($this->uploaderHelper->asset($entity, 'imageFile'));
    }
}