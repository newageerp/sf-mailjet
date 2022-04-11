<?php

namespace Newageerp\SfMailjet\Service;

use Doctrine\ORM\EntityManagerInterface;
use Mailjet\Resources;
use Newageerp\SfMail\Service\MailSendService;

class MailjetSendMailService extends MailSendService
{
    protected MailjetService $mailjetService;

    public function __construct(EntityManagerInterface $em, MailjetService $mailjetService)
    {
        parent::__construct($em);
        $this->mailjetService = $mailjetService;
    }

    public function sendMail(
        string $fromName,
        string $fromEmail,
        string $subject,
        string $content,
        array $recipients,
        ?array $attachments = [],
        string $customId = '',
    ) {
        $recipientMap = array_map(function (string $email) {
            return ['Email' => $email];
        }, $recipients);

        $mj = $this->mailjetService->getClient();
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => $fromEmail,
                        'Name' => $fromName,
                    ],
                    'To' => $recipientMap,
                    'Subject' => $subject,
                    'HTMLPart' => $content,
                    'CustomID' => $customId,
                    'Attachments' => $attachments,
                ],
            ],

        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        if (!$response->success()) {
            throw new \Exception('Mail send error');
        }
    }
}