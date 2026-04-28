<?php

namespace App\Services\Chat;

use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

/**
 * Appel fournisseur LLM F20 (API compatible OpenAI chat/completions).
 */
final class LlmChatCompletionService
{
    /**
     * @param  array<int, array{role: string, content: string}>  $messages
     */
    public function complete(string $systemPrompt, array $messages): string
    {
        $driver = config('llm.driver', 'openai_compat');

        if ($driver === 'fake') {
            return $this->fakeReply($messages);
        }

        $cfg = config('llm.openai_compat', []);
        $apiKey = $cfg['api_key'] ?? null;
        if (! is_string($apiKey) || $apiKey === '') {
            throw new RuntimeException('Clé API LLM absente : définissez OPENAI_API_KEY ou LLM_DRIVER=fake.');
        }

        $base = $cfg['base_url'] ?? 'https://api.openai.com/v1';
        $model = $cfg['model'] ?? 'gpt-4o-mini';
        $timeout = (int) ($cfg['timeout'] ?? 45);

        $payload = [
            'model' => $model,
            'temperature' => 0.3,
            'messages' => array_merge(
                [['role' => 'system', 'content' => $systemPrompt]],
                $messages,
            ),
        ];

        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->timeout($timeout)
                ->post($base.'/chat/completions', $payload);
        } catch (Throwable $e) {
            throw new RuntimeException('Réseau LLM indisponible.', 0, $e);
        }

        if (! $response->successful()) {
            throw new RuntimeException('LLM HTTP '.$response->status());
        }

        $content = data_get($response->json(), 'choices.0.message.content');
        if (! is_string($content) || $content === '') {
            throw new RuntimeException('Réponse LLM vide ou invalide.');
        }

        return trim($content);
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $messages
     */
    private function fakeReply(array $messages): string
    {
        $last = '';
        foreach (array_reverse($messages) as $m) {
            if (($m['role'] ?? '') === 'user') {
                $last = (string) ($m['content'] ?? '');

                break;
            }
        }

        $lower = mb_strtolower($last);

        if (str_contains($lower, 'allerg') || str_contains($lower, 'urgence')) {
            return 'Pour toute question d’allergie grave ou d’urgence médicale, adressez-vous directement au personnel du restaurant. Les informations du chat sont indicatives.';
        }

        if (str_contains($lower, 'réserver') || str_contains($lower, 'reservation')) {
            return 'Vous pouvez utiliser la page Réservation du site pour déposer une demande. Le personnel confirmera les disponibilités.';
        }

        return 'Merci pour votre question. Les informations sur la carte ci-dessus sont indicatives : vérifiez auprès du service pour les prix du jour et les allergènes.';
    }
}
