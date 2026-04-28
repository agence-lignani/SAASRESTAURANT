<?php

namespace App\Support\Filament;

use Illuminate\Support\Arr;

/**
 * Corrige les états chargés depuis la BDD pour Filament RichEditor / TipTap PHP :
 * - document { type: doc } sans clé "content"
 * - tableaux vides [] aux feuilles des champs riches (anciens Textarea / données corrompues)
 */
final class SiteContentRichEditorStateNormalizer
{
    private const RICH_TEXT_PATH_SUFFIX = '/\.(subtitle|body|quote|description|intro|success_message|availability_help|slots_help|paragraph|answer)$/';

    /**
     * Valeur d’un seul champ RichEditor / TipTap : évite setContent([]) ou { type: doc } sans content
     * (Filament RichEditor + RichEditorStateCast appellent tous les deux Tiptap\Editor::setContent).
     *
     * @return array<string, mixed>|string
     */
    public static function normalizeRichEditorLeafValue(mixed $state): mixed
    {
        if ($state === null || $state === []) {
            return [
                'type' => 'doc',
                'content' => [],
            ];
        }

        if (! is_array($state)) {
            return $state;
        }

        if (($state['type'] ?? null) === 'doc') {
            if (! array_key_exists('content', $state)) {
                $state['content'] = [];
            } elseif (! is_array($state['content'])) {
                $state['content'] = [];
            }

            return $state;
        }

        // Fragment (paragraph, heading, etc.) : TipTap PHP attend une racine doc avec "content"
        if (! array_key_exists('content', $state)) {
            return [
                'type' => 'doc',
                'content' => [$state],
            ];
        }

        return $state;
    }

    public static function richEditorLeafNeedsNormalization(mixed $state): bool
    {
        if ($state === null || $state === []) {
            return true;
        }

        if (! is_array($state)) {
            return false;
        }

        if (($state['type'] ?? null) === 'doc') {
            return ! array_key_exists('content', $state) || ! is_array($state['content'] ?? null);
        }

        return ! array_key_exists('content', $state);
    }

    /**
     * @param  array<string, mixed>  $content
     * @return array<string, mixed>
     */
    public static function normalize(array $content): array
    {
        $content = self::ensureDocHasContentRecursive($content);

        $flat = Arr::dot($content);
        foreach ($flat as $path => $value) {
            if (! preg_match(self::RICH_TEXT_PATH_SUFFIX, $path)) {
                continue;
            }
            if ($value === [] || $value === null) {
                Arr::set($content, $path, [
                    'type' => 'doc',
                    'content' => [],
                ]);
            }
        }

        return $content;
    }

    /**
     * @param  array<string|int, mixed>  $data
     * @return array<string|int, mixed>
     */
    private static function ensureDocHasContentRecursive(array $data): array
    {
        if (isset($data['type']) && $data['type'] === 'doc' && ! array_key_exists('content', $data)) {
            $data['content'] = [];
        }

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = self::ensureDocHasContentRecursive($value);
            }
        }

        return $data;
    }
}
