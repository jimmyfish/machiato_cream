<?php

namespace App\Service\Mailer;

use App\Service\Registry\FolderRegistryService;
use Swift_Attachment;
use Swift_Mailer;
use Swift_Message;
use Swift_SwiftException;

class SendEmailService
{
    private $mailer;
    private $folderRegistryService;

    public function __construct(
        Swift_Mailer $mailer,
        FolderRegistryService $folderRegistryService
    ) {
        $this->mailer = $mailer;
        $this->folderRegistryService = $folderRegistryService;
    }

    public function send($to, $subject = "", $carbon = [], $content = "", $attachment = null)
    {
        try {
            $message = (new Swift_Message($subject))
                ->setTo($to)
                ->setBody("Hello world!");

            $fullPath = $this->folderRegistryService->getOutputPath($attachment);

            $message->attach(Swift_Attachment::fromPath($fullPath)->setFilename($attachment));

            $this->mailer->send($message);

            return true;
        } catch (Swift_SwiftException $exception) {
            dump($exception->getMessage());
            return false;
        }
    }
}
