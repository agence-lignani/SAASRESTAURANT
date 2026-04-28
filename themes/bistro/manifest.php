<?php

/**
 * Manifeste thème Bistro — source de vérité (CDC §2.6.3).
 * Les écrans Filament et le site public s’appuieront sur cette structure en J2+.
 */
return [
    'meta' => [
        'id' => 'bistro',
        'name' => 'Bistro',
        'version' => '0.1.0',
        'design_reference' => 'UI type export Google Stitch (Material 3, cartes arrondies, hero 2 colonnes) ; preview projet node-id dans Stitch ; tokens tenant conservés.',
    ],
    'tokens' => [
        'colors' => [
            'primary' => '#8B4513',
            'secondary' => '#2C1810',
            'text' => '#1a1a1a',
        ],
        'radius' => [
            'sm' => '0.25',
            'md' => '0.5',
            'lg' => '1',
        ],
        'shadows' => [
            'sm' => '0 1px 2px rgb(0 0 0 / 0.06)',
            'md' => '0 4px 12px rgb(0 0 0 / 0.08)',
        ],
    ],
    'blocks' => [
        'home_order' => [
            'Block_HeroBrand',
            'Block_ManifestoTeaser',
            'Block_FeatureGrid_NosX',
            'Block_SpotlightQuote',
        ],
    ],
    'slots' => [
        'home.hero' => [
            'id' => 'home.hero',
            'ratio' => '21/9',
            'required_on' => ['home'],
            'alt_required' => true,
        ],
    ],
    'widgets' => [
        'chat_f20' => [
            'enabled_by_default' => false,
            'position' => 'bottom-end',
        ],
    ],
];
