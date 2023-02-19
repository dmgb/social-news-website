<?php declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Message;
use App\Helper\DateTimeFormatter;

class MessageNormalizer extends AbstractNormalizer
{
    public function normalize($object, string $format = null, array $context = []): array
    {
        $message = $object;
        if (!$message instanceof Message) {
            throw new \TypeError('Argument $object must be of type ' . Message::class);
        }

        $shortId = $message->getShortId();

        return [
            'id' => $message->getId(),
            'shortId' => $shortId,
            'sender' => $this->normalizeUser($message->getSender()),
            'createdAt' => DateTimeFormatter::timeAgo($message->getCreatedAt()),
            'subject' => $message->getSubject(),
            'body' => $message->getBody(),
            'state' => $message->getState(),
            'urls' => [
                'show' => $this->router->generate('message_show', ['shortId' => $shortId]),
                'changeState' => $this->router->generate('message_change_state', ['shortId' => $shortId]),
            ],
        ];
    }
}
