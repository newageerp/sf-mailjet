<?php

namespace Newageerp\SfMailjet\Service;

use Mailjet\Resources;
use Newageerp\SfMail\Service\MailSendService;
use Mailjet\Client;

class MailjetSendMailService extends MailSendService
{
    protected ?Client $client = null;

    public function sendMail(
        string $fromName,
        string $fromEmail,
        string $subject,
        string $content,
        array  $recipients,
        ?array $attachments = [],
        string $customId = '',
    )
    {
        $recipientMap = array_map(function (string $email) {
            return ['Email' => $email];
        }, $recipients);

        $mj = $this->getClient();
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
            throw new \Exception('Error ' . json_encode($response->getBody()) . ' ' . json_encode($body));
        }
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        if (!$this->client) {
            $this->client = new Client($_ENV['NAE_SFS_MAILJET_PUBLIC_KEY'], $_ENV['NAE_SFS_MAILJET_PRIVATE_KEY'], true, ['version' => 'v3']);
        }
        return $this->client;
    }
}