<?php

namespace AppBundle\Service;


class MailerService
{
    /**
     * @var \Swift_Mailer
     */
    protected $mailer;
    /**
     * @var \Swift_Message
     */
    protected $message;
    /**
     * @var string
     */
    protected $subject;
    /**
     * @var string
     */
    protected $from;
    /**
     * @var string
     */
    protected $to;
    /**
     * @var string
     */
    protected $body;
    /**
     * @var array | null
     */
    protected $attachment = null;

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param mixed $subject
     * @return MailerService
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param mixed $from
     * @return MailerService
     */
    public function setFrom($from)
    {
        $this->from = $from;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param mixed $to
     * @return MailerService
     */
    public function setTo($to)
    {
        $this->to = $to;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     * @return MailerService
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getAttachment(): array
    {
        return $this->attachment;
    }

    /**
     * @param array|null $attachment
     * @return MailerService
     */
    public function setAttachment(array $attachment): MailerService
    {
        $this->attachment = $attachment;
        return $this;
    }

    /**
     * MailerService constructor.
     * @param \Swift_Mailer $mailer
     * @param \Swift_Message $message
     */
    public function __construct(\Swift_Mailer $mailer, \Swift_Message $message)
    {
        $this->mailer = $mailer;
        $this->message = $message;
    }

    /**
     * @return $this
     */
    public function attach() {
        $attachment = $this->getAttachment();
        if (!$attachment or empty($attachment)) {
            return $this;
        }
        foreach ($attachment as $attach) {
            $this->message->attach(\Swift_Attachment::fromPath($attach));
        }

        return $this;
    }

    /**
     * @return bool|string
     */
    public function sendMessage() {

        $message = $this->message
            ->setSubject($this->getSubject())
            ->setFrom($this->getFrom())
            ->setTo($this->getTo())
            ->setBody($this->getBody());

        try {
            $this->mailer->send($message);
            return true;
        } catch (\Swift_SwiftException $exception) {
            return $exception->getMessage();
        }
    }
}