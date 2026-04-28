<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\Restaurant;
use App\Models\RestaurantChatSetting;
use App\Services\Chat\LlmChatCompletionService;
use App\Services\Chat\MenuContextBuilder;
use App\Support\Chat\ChatIpHasher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

class ChatController extends Controller
{
    public function __construct(
        private MenuContextBuilder $menuContextBuilder,
        private LlmChatCompletionService $llm,
    ) {}

    public function store(Request $request): JsonResponse
    {
        /** @var Restaurant $restaurant */
        $restaurant = $request->attributes->get('restaurant');

        $settings = RestaurantChatSetting::query()
            ->where('restaurant_id', $restaurant->id)
            ->first();

        if ($settings === null || ! $settings->is_enabled) {
            return response()->json([
                'message' => 'L’assistant conversationnel est désactivé pour cet établissement.',
            ], 404);
        }

        $maxLen = max(256, min(8000, (int) $settings->max_user_message_length));

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:'.$maxLen],
            'session_token' => ['nullable', 'string', 'max:64'],
        ]);

        $ip = $request->ip();
        $ipHash = ChatIpHasher::hash($ip);

        $session = $this->resolveSession(
            restaurant: $restaurant,
            token: $validated['session_token'] ?? null,
            ipHash: $ipHash,
        );

        if ($this->sessionUserMessageCount($session->id) >= $settings->max_messages_per_session) {
            return response()->json([
                'message' => 'Limite de messages atteinte pour cette session. Rechargez la page pour recommencer.',
            ], 429);
        }

        if ($this->todayUserMessagesCountForIp($restaurant->id, $ipHash) >= $settings->max_messages_per_day_per_ip) {
            return response()->json([
                'message' => 'Limite quotidienne atteinte. Réessayez demain.',
            ], 429);
        }

        $userContent = $validated['message'];

        ChatMessage::query()->create([
            'chat_session_id' => $session->id,
            'role' => 'user',
            'content' => $userContent,
            'created_at' => now(),
        ]);

        $session->forceFill(['last_activity_at' => now()])->save();

        $menuContext = $this->menuContextBuilder->build($restaurant);
        $systemPrompt = $this->buildSystemPrompt($restaurant, $settings, $menuContext);

        $history = $this->tailMessagesForLlm($session->id, (int) $settings->history_tail_messages);

        $llmMessages = [];
        foreach ($history as $row) {
            $role = $row['role'] === 'assistant' ? 'assistant' : 'user';
            $llmMessages[] = ['role' => $role, 'content' => $row['content']];
        }

        try {
            $reply = $this->llm->complete($systemPrompt, $llmMessages);
        } catch (Throwable) {
            return response()->json([
                'message' => 'L’assistant est temporairement indisponible. Réessayez plus tard ou contactez le restaurant.',
            ], 503);
        }

        ChatMessage::query()->create([
            'chat_session_id' => $session->id,
            'role' => 'assistant',
            'content' => $reply,
            'created_at' => now(),
        ]);

        return response()->json([
            'reply' => $reply,
            'session_token' => $session->token,
            'disclaimer' => 'Les informations sont indicatives et basées sur la carte en ligne — pour les allergènes, urgences ou détails, adressez-vous au restaurant.',
        ]);
    }

    private function resolveSession(Restaurant $restaurant, ?string $token, string $ipHash): ChatSession
    {
        if (is_string($token) && $token !== '') {
            $existing = ChatSession::query()
                ->where('restaurant_id', $restaurant->id)
                ->where('token', $token)
                ->first();

            if ($existing !== null) {
                return $existing;
            }
        }

        return ChatSession::query()->create([
            'restaurant_id' => $restaurant->id,
            'token' => Str::lower(Str::random(48)),
            'ip_hash' => $ipHash,
            'last_activity_at' => now(),
        ]);
    }

    private function sessionUserMessageCount(int $sessionId): int
    {
        return ChatMessage::query()
            ->where('chat_session_id', $sessionId)
            ->where('role', 'user')
            ->count();
    }

    private function todayUserMessagesCountForIp(int $restaurantId, string $ipHash): int
    {
        return ChatMessage::query()
            ->where('role', 'user')
            ->whereDate('created_at', now()->toDateString())
            ->whereHas('chatSession', function ($q) use ($restaurantId, $ipHash): void {
                $q->where('restaurant_id', $restaurantId)->where('ip_hash', $ipHash);
            })
            ->count();
    }

    /**
     * @return list<array{role: string, content: string}>
     */
    private function tailMessagesForLlm(int $sessionId, int $maxMessages): array
    {
        $maxMessages = max(2, min(100, $maxMessages));

        $rows = ChatMessage::query()
            ->where('chat_session_id', $sessionId)
            ->orderByDesc('id')
            ->limit($maxMessages)
            ->get(['role', 'content'])
            ->reverse()
            ->values()
            ->all();

        /** @var list<array{role: string, content: string}> */
        return array_map(fn ($r) => [
            'role' => (string) $r->role,
            'content' => (string) $r->content,
        ], $rows);
    }

    private function buildSystemPrompt(Restaurant $restaurant, RestaurantChatSetting $settings, string $menuContext): string
    {
        $base = <<<'PROMPT'
Tu es l’assistant conversationnel du restaurant indiqué ci-dessous. Tu réponds en français.
Règles strictes :
- Tu t’appuies UNIQUEMENT sur le menu fourni dans le bloc « Carte ». Ne crée pas de plats, ni de prix, ni de disponibilités qui ne figurent pas dans ce bloc.
- Si une information manque dans la carte, dis-le clairement et propose de contacter le restaurant ou la page réservation.
- Les allergènes : rappelle que les données affichées sont indicatives ; en cas de risque, le client doit s’adresser au personnel.
- Pas de conseils médicaux ; en urgence, orienter vers les services compétents ou le personnel sur place.
- Reste concis et professionnel. Pour réserver ou écrire : indique les pages « Réservation » et « Contact » du site.
PROMPT;

        $extra = filled($settings->system_prompt_extra)
            ? "\nConsignes supplémentaires de l’établissement :\n".$settings->system_prompt_extra
            : '';

        return trim($base."\n\nÉtablissement : ".$restaurant->name.$extra."\n\n## Carte\n".$menuContext);
    }
}
