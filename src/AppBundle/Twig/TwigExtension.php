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

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('strtotime', [$this, 'strtotime']),
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

    /**
     * @param string $date
     * @return \DateTime
     */
    public function strtotime(string $date) {

        $timestamp = strtotime($date);

        $dateTime = new \DateTime();

        $time = $dateTime->setTimestamp($timestamp);

        return $time;

    }
}