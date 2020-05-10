<?php

namespace Dashford\Soundscape\Subscriber;

use Dashford\Soundscape\Event\ArtistCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ArtistSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            ArtistCreatedEvent::NAME => 'onArtistCreate'
        ];
    }

    public function onArtistCreate($event)
    {

    }
}