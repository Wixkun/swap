<?php

namespace App\Serializer;

use App\Entity\Agent;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;

class AgentSerializer implements ContextAwareDenormalizerInterface
{
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): mixed
    {
        if ($type !== Agent::class) {
            return null;
        }

        $agent = new Agent();
        $agent->setPseudo($data['pseudo'] ?? 'Inconnu');
        $agent->setPhoneNumber($data['phoneNumber'] ?? 'N/A');
        $agent->setRatingGlobal($data['ratingGlobal'] ?? null);

        return $agent;
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return $type === Agent::class;
    }
}
