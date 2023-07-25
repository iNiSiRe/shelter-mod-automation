<?php

namespace Shelter\Automation\Application\Notification\Service;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Log\LoggerInterface;
use function RingCentral\Psr7\stream_for;

class Telegram
{
    public function __construct(
        private readonly string                  $apiKey,
        private readonly ClientInterface         $httpClient,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly LoggerInterface         $logger
    )
    {
    }

    public function sendMessage(string $chatId, string $message): void
    {
        $request = $this->requestFactory->createRequest('POST', 'https://api.telegram.org/bot' . $this->apiKey . '/sendMessage');

        $content = json_encode([
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'html',
        ]);

        $request = $request
            ->withHeader('Content-Type', 'application/json')
            ->withBody(stream_for($content));

        try {
            $response = $this->httpClient->sendRequest($request);
            if ($response->getStatusCode() === 200) {
                $this->logger->debug('Send message success', ['message' => $message]);
            } else {
                $this->logger->error('Send message error', ['status_code' => $response->getStatusCode(), 'content' => $response->getBody()->getContents()]);
            }
        } catch (\Exception $e) {
            $this->logger->error('Uncaught exception', ['class' => $e::class, 'message' => $e->getMessage()]);
        }
    }
}