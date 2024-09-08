<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Notifier\Bridge\Sweego;

use Symfony\Component\Notifier\Exception\TransportException;
use Symfony\Component\Notifier\Exception\UnsupportedMessageTypeException;
use Symfony\Component\Notifier\Message\MessageInterface;
use Symfony\Component\Notifier\Message\SentMessage;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\Transport\AbstractTransport;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @author Mathieu Santostefano <msantostefano@protonmail.com>
 */
final class SweegoTransport extends AbstractTransport
{
    protected const HOST = 'api.sweego.io';

    public function __construct(
        #[\SensitiveParameter] private string $apiKey,
        ?HttpClientInterface $client = null,
        ?EventDispatcherInterface $dispatcher = null,
    ) {
        parent::__construct($client, $dispatcher);
    }

    public function __toString(): string
    {
        return \sprintf('sweego://%s', $this->getEndpoint());
    }

    public function supports(MessageInterface $message): bool
    {
        return $message instanceof SmsMessage && null === $message->getOptions();
    }

    protected function doSend(MessageInterface $message): SentMessage
    {
        if (!$message instanceof SmsMessage) {
            throw new UnsupportedMessageTypeException(__CLASS__, SmsMessage::class, $message);
        }

        $endpoint = \sprintf('https://%s/send', $this->getEndpoint());
        $response = $this->client->request('POST', $endpoint, [
            'headers' => [
                'Api-Key' => $this->apiKey,
            ],
            'json' => [
                'recipients' => [
                    [
                        'num' => $message->getPhone(),
                        'region' => 'FR',
                    ],
                ],
                'message-txt' => $message->getSubject(),
                'channel' => 'sms',
                'campaign-type' => 'transac',
                'provider' => 'sweego',
            ],
        ]);

        try {
            $statusCode = $response->getStatusCode();
        } catch (TransportExceptionInterface $e) {
            throw new TransportException('Could not reach the remote Sweego server.', $response, 0, $e);
        }

        if (200 !== $statusCode) {
            throw new TransportException('Unable to send the SMS.', $response);
        }

        $success = $response->toArray(false);

        $sentMessage = new SentMessage($message, (string) $this);
        $sentMessage->setMessageId(array_values($success['swg_uids'])[0]);

        return $sentMessage;
    }
}
