<?php

namespace App\Support\SiteContent;

use Illuminate\Support\HtmlString;

/**
 * Affichage sécurisé des contenus riches (WYSIWYG) issus du back-office.
 */
final class SiteContentHtml
{
    private const ALLOWED_TAGS = '<p><br><br/><strong><b><em><i><u><a><ul><ol><li><span><h2><h3><blockquote><div>';

    public static function safe(?string $value): HtmlString
    {
        if ($value === null || trim($value) === '') {
            return new HtmlString('');
        }

        return new HtmlString(strip_tags(trim($value), self::ALLOWED_TAGS));
    }

    /**
     * Paragraphe ou bloc : si le texte est sans balise (anciennes données), on l’encapsule dans un &lt;p&gt;.
     */
    public static function paragraph(?string $value): HtmlString
    {
        if ($value === null || trim($value) === '') {
            return new HtmlString('');
        }

        $trimmed = trim($value);

        if (! str_contains($trimmed, '<')) {
            return new HtmlString('<p>'.e($trimmed).'</p>');
        }

        return self::safe($trimmed);
    }
}
