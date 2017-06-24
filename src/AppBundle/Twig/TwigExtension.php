<?php

namespace AppBundle\Twig;


use AppBundle\Entity\Booking;

class TwigExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('unreadMessages', [$this, 'unreadMessages']),
        ];
    }

    public function unreadMessages($messages) {
        $unread = [];

        /** @var Booking $message */
        foreach ($messages as $message) {
             if (!$message->isStatus()) {
                 $unread[] = $message;
             }
        }

        return count($unread);
    }
}