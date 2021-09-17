<?php

namespace App\Service\Mailer;

use Swift_Mailer;
use Swift_Message;
use Swift_Attachment;
use Swift_SwiftException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Email;
use App\Service\Registry\FolderRegistryService;
use Symfony\Component\Mailer\MailerInterface;

class SendEmailService
{
    private $mailer;
    private $swiftMailer;
    private $folderRegistryService;

    public function __construct(
        Swift_Mailer $swiftMailer,
        FolderRegistryService $folderRegistryService,
        MailerInterface $mailer
    )
    {
        $this->swiftMailer = $swiftMailer;
        $this->mailer = $mailer;
        $this->folderRegistryService = $folderRegistryService;
    }

    public function send($to, $subject = "", $carbon = [], $content = "", $attachment = null)
    {
        $email = (new Email())
            ->from("dito@tuta.io")
            ->to($to)
            ->subject($subject)
            ->text("Hello there!")
            ->attach(fopen($attachment, 'r'));
        try {
            $this->mailer->send($email);

            return true;
        } catch (TransportExceptionInterface $e) {
            dump($e->getMessage());
            return false;
        } catch (\Exception $exception) {
            dump($exception->getMessage());
            return false;
        }
    }
}
