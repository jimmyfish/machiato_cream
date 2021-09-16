<?php

namespace App\Service\Mailer;

use Swift_Mailer;
use Swift_Message;
use Swift_Attachment;
use Swift_SwiftException;
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
    ) {
        $this->swiftMailer = $swiftMailer;
        $this->mailer = $mailer;
        $this->folderRegistryService = $folderRegistryService;
    }

    public function send($to, $subject = "", $carbon = [], $content = "", $attachment = null)
    {
        try {
            $fullPath = $this->folderRegistryService->getOutputPath($attachment);
            $email = (new Email())
                ->from("dito@tuta.io")
                ->to($to)
                ->subject($subject)
                ->text("Hello there!")
                ->attachFromPath($fullPath);

            $this->mailer->send($email);

            return true;
        } catch (\Exception $exception) {
            dump($exception->getMessage());
            return false;
        }
    }

    public function swift($to, $subject = "", $carbon = [], $content = "", $attachment = null)
    {
        try {
            $message = (new Swift_Message($subject))
                ->setTo($to)
                ->setBody("Hello world!");

            $fullPath = $this->folderRegistryService->getOutputPath($attachment);

            $message->attach(Swift_Attachment::fromPath($fullPath)->setFilename($attachment));

            $this->swiftMailer->send($message);

            return true;
        } catch (Swift_SwiftException $exception) {
            dump($exception->getMessage());
            return false;
        }
    }
}
