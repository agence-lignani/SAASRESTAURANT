<?php

namespace App\Filament\Resources\SiteContents\Schemas;

use App\Filament\Support\FilamentImageUpload;
use App\Support\SiteContent\PageSectionCatalog;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class SiteContentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Pages du site')
                ->columnSpanFull()
                ->persistTabInQueryString('content-tab')
                ->tabs([
                    Tab::make('Accueil')
                        ->schema(self::homeTab()),
                    Tab::make('Carte')
                        ->schema(self::carteTab()),
                    Tab::make('Galerie')
                        ->schema(self::galerieTab()),
                    Tab::make('Contact')
                        ->schema(self::contactTab()),
                    Tab::make('Réservation')
                        ->schema(self::reservationTab()),
                    Tab::make('Gestion réservation client')
                        ->schema(self::reservationManageTab()),
                ]),
        ]);
    }

    /**
     * @return array<int, Section>
     */
    private static function homeTab(): array
    {
        return [
            Section::make('Structure de la page d’accueil')
                ->schema([
                    ViewField::make('bistro_home_block_notice')
                        ->view('filament.forms.bistro-home-content-notice')
                        ->dehydrated(false)
                        ->columnSpanFull(),
                ])
                ->columns(1)
                ->compact(false),

            Section::make('Ordre des sections sur la page d’accueil')
                ->description('Déplacez les lignes pour changer l’ordre des blocs. Les champs à l’intérieur de chaque bloc (paragraphes, boutons, etc.) suivent l’ordre défini dans le thème et ne sont pas réordonnables.')
                ->schema([
                    Repeater::make('content.home.section_order')
                        ->label('')
                        ->schema([
                            Select::make('section')
                                ->label('Section')
                                ->options(PageSectionCatalog::options('home'))
                                ->required(),
                        ])
                        ->reorderable()
                        ->addable(false)
                        ->deletable(false)
                        ->minItems(count(PageSectionCatalog::keys('home')))
                        ->maxItems(count(PageSectionCatalog::keys('home')))
                        ->columnSpanFull(),
                ])
                ->columns(1)
                ->compact(false),

            self::pageSection('home', 'hero', Section::make('Accueil — bandeau principal')
                ->description('Première impression : nom du lieu, accroche, photo et accès rapide à la carte ou à la réservation.')
                ->schema([
                    self::variantSelect('content.home.hero.variant', 'Variante visuelle', [
                        'hero_split_editorial' => 'Hero Split Editorial',
                        'hero_full_bleed_cinema' => 'Hero Full Bleed Cinema',
                        'hero_minimal_mono' => 'Hero Minimal Mono',
                        'hero_card_glass' => 'Hero Card Glass',
                        'bakery_hero_classic' => 'Bakery Hero Classic',
                    ]),
                    FilamentImageUpload::withAutomaticCompression(
                        FileUpload::make('content.home.hero.image_url')
                            ->label('Image du hero')
                            ->image()
                            ->disk('public')
                            ->directory('site-content/hero')
                            ->visibility('public')
                            ->imageEditor()
                            ->helperText('Image principale affichée dans le bandeau. Si vide, un visuel par défaut est utilisé. Compression automatique pour rester sous le plafond du site.')
                            ->columnSpanFull()
                    ),
                    TextInput::make('content.home.hero.image_alt')
                        ->label('Texte alternatif de l’image (accessibilité)')
                        ->maxLength(180)
                        ->helperText('Décrit brièvement l’image pour les lecteurs d’écran.')
                        ->columnSpanFull(),
                    TextInput::make('content.home.hero.eyebrow')
                        ->label('Étiquette (au-dessus du titre)')
                        ->placeholder('Depuis 1985')
                        ->maxLength(80)
                        ->helperText('Petit texte au-dessus du titre (ex. « Bienvenue »). Laissez vide pour ne pas l’afficher.')
                        ->columnSpanFull(),
                    TextInput::make('content.home.hero.title')
                        ->label('Titre')
                        ->placeholder('Le Coin Parisien')
                        ->maxLength(160)
                        ->columnSpanFull(),
                    RichEditor::make('content.home.hero.subtitle')
                        ->label('Sous-titre')
                        ->placeholder('Une table où l’on célèbre le marché : produits d’exception et service attentionné.')
                        ->columnSpanFull(),
                    self::ctaButtonsRepeater('content.home.hero.cta_buttons', 'Boutons'),
                ])
                ->columns(1) // 1 colonne = plus d’air vertical entre les blocs
                ->compact(false)),

            self::pageSection('home', 'manifesto', Section::make('À propos / Histoire / Chef')
                ->description('Section « À propos du restaurant » : sur-titre, récit, visuel et lien.')
                ->schema([
                    self::variantSelect('content.home.manifesto.variant', 'Variante visuelle', [
                        'about_image_stack' => 'About Image Stack',
                        'about_chapter_layout' => 'About Chapter Layout',
                        'about_cinematic_panel' => 'About Cinematic Panel',
                        'bakery_about_band' => 'Bakery About Band',
                    ]),
                    TextInput::make('content.home.manifesto.eyebrow')
                        ->label('Sur-titre (ex. Notre histoire)')
                        ->placeholder('Notre histoire')
                        ->maxLength(80)
                        ->columnSpanFull(),
                    TextInput::make('content.home.manifesto.heading')->label('Titre')->placeholder('Une table ancrée dans son quartier')->maxLength(160),
                    FilamentImageUpload::withAutomaticCompression(
                        FileUpload::make('content.home.manifesto.image_url')
                            ->label('Image du bloc')
                            ->image()
                            ->disk('public')
                            ->directory('site-content/manifesto')
                            ->visibility('public')
                            ->imageEditor()
                            ->helperText('Façade, rue, salle ou équipe — renforce le récit « ancré dans le quartier ».')
                            ->columnSpanFull()
                    ),
                    TextInput::make('content.home.manifesto.image_alt')
                        ->label('Texte alternatif de l’image')
                        ->maxLength(180)
                        ->columnSpanFull(),
                    self::paragraphsRepeater('content.home.manifesto.paragraphs'),
                    TextInput::make('content.home.manifesto.signature')->label('Signature')->maxLength(120),
                    self::linkButtonsRepeater('content.home.manifesto.more_links', 'Liens « en savoir plus »'),
                ])
                ->columns(1)
                ->compact(false)),

            self::pageSection('home', 'carte_narrative', Section::make('Carte mise en avant (plats phares)')
                ->description('Section « Carte signature » : titre narratif, visuel, paragraphes et CTA.')
                ->schema([
                    self::variantSelect('content.home.carte_narrative.variant', 'Variante visuelle', [
                        'menu_featured_primary' => 'Menu Featured Primary',
                        'menu_grid_minimal' => 'Menu Grid Minimal',
                    ]),
                    TextInput::make('content.home.carte_narrative.eyebrow')
                        ->label('Sur-titre (petit libellé)')
                        ->placeholder('Carte signature')
                        ->maxLength(80)
                        ->columnSpanFull(),
                    TextInput::make('content.home.carte_narrative.heading')->label('Titre')->placeholder('Une carte entre tradition de brasserie et produits du moment')->maxLength(160),
                    FilamentImageUpload::withAutomaticCompression(
                        FileUpload::make('content.home.carte_narrative.image_url')
                            ->label('Image du bloc')
                            ->image()
                            ->disk('public')
                            ->directory('site-content/carte-narrative')
                            ->visibility('public')
                            ->imageEditor()
                            ->helperText('Photo d’assiette, de cuisine ou ambiance — affichée à gauche sur grand écran.')
                            ->columnSpanFull()
                    ),
                    TextInput::make('content.home.carte_narrative.image_alt')
                        ->label('Texte alternatif de l’image')
                        ->maxLength(180)
                        ->helperText('Obligatoire pour l’accessibilité si une image est présente.')
                        ->columnSpanFull(),
                    self::paragraphsRepeater('content.home.carte_narrative.paragraphs'),
                    self::ctaButtonsRepeater('content.home.carte_narrative.cta_buttons', 'Boutons'),
                ])
                ->columns(1)
                ->compact(false)),

            self::pageSection('home', 'menus', Section::make('Menus & formules')
                ->description('Présentez les formules phares (midi, soir, brunch, groupe).')
                ->schema([
                    self::variantSelect('content.home.menus.variant', 'Variante visuelle', [
                        'menu_featured_primary' => 'Menu Featured Primary',
                        'menu_masonry_cards' => 'Menu Masonry Cards',
                        'menu_grid_minimal' => 'Menu Grid Minimal',
                        'bakery_top_products' => 'Bakery Top Products',
                    ]),
                    TextInput::make('content.home.menus.heading')->label('Titre')->placeholder('Signatures culinaires')->maxLength(160),
                    TextInput::make('content.home.menus.intro')->label('Introduction courte')->placeholder('Une sélection des assiettes qui racontent notre cuisine : saison, précision et générosité.')->maxLength(220)->columnSpanFull(),
                    Repeater::make('content.home.menus.items')
                        ->label('Plats / signatures')
                        ->schema([
                            TextInput::make('title')->label('Nom du menu')->required()->maxLength(120),
                            TextInput::make('price')->label('Prix / indicateur tarifaire')->maxLength(60),
                            TextInput::make('description')->label('Description courte')->maxLength(220)->columnSpanFull(),
                            FilamentImageUpload::withAutomaticCompression(
                                FileUpload::make('image_url')
                                    ->label('Image')
                                    ->image()
                                    ->disk('public')
                                    ->directory('site-content/menus')
                                    ->visibility('public')
                                    ->imageEditor()
                                    ->columnSpanFull()
                            ),
                            TextInput::make('image_alt')->label('Texte alternatif')->maxLength(180)->columnSpanFull(),
                        ])
                        ->minItems(0)
                        ->maxItems(10)
                        ->defaultItems(0)
                        ->columns(1)
                        ->columnSpanFull(),
                    self::ctaButtonsRepeater('content.home.menus.cta_buttons', 'Boutons'),
                ])
                ->columns(1)
                ->compact(false)),

            self::pageSection('home', 'gallery_highlights', Section::make('Galerie ambiance')
                ->description('Aperçu visuel rapide sur la page d’accueil.')
                ->schema([
                    self::variantSelect('content.home.gallery_highlights.variant', 'Variante visuelle', [
                        'gallery_bento' => 'Gallery Bento',
                        'gallery_film_strip' => 'Gallery Film Strip',
                        'gallery_collage_editorial' => 'Gallery Collage Editorial',
                        'bakery_explore_grid' => 'Bakery Explore Grid',
                    ]),
                    TextInput::make('content.home.gallery_highlights.heading')->label('Titre')->placeholder('L’esprit & la Maison')->maxLength(160),
                    TextInput::make('content.home.gallery_highlights.intro')->label('Introduction courte')->placeholder('Salle principale, cave, cuisine ouverte et salon : chaque espace participe à l’expérience.')->maxLength(220)->columnSpanFull(),
                    Repeater::make('content.home.gallery_highlights.items')
                        ->label('Visuels')
                        ->schema([
                            FilamentImageUpload::withAutomaticCompression(
                                FileUpload::make('image_url')
                                    ->label('Image')
                                    ->image()
                                    ->disk('public')
                                    ->directory('site-content/home-gallery')
                                    ->visibility('public')
                                    ->imageEditor()
                            ),
                            TextInput::make('image_alt')->label('Texte alternatif')->maxLength(180),
                            TextInput::make('caption')->label('Légende')->maxLength(160),
                        ])
                        ->minItems(0)
                        ->maxItems(6)
                        ->defaultItems(0)
                        ->columns(1)
                        ->columnSpanFull(),
                    Grid::make(2)
                        ->schema([
                            TextInput::make('content.home.gallery_highlights.gallery_link_label')->label('Libellé du lien vers la galerie')->maxLength(100),
                            TextInput::make('content.home.gallery_highlights.gallery_link_href')->label('URL / ancre')->maxLength(255),
                        ]),
                ])
                ->columns(1)
                ->compact(false)),

            self::pageSection('home', 'espaces', Section::make('Événements & privatisation')
                ->description('Section éditoriale (chef / équipe / reconnaissance) avec visuel.')
                ->schema([
                    self::variantSelect('content.home.espaces.variant', 'Variante visuelle', [
                        'about_founder_focus' => 'About Founder Focus',
                        'about_side_label' => 'About Side Label',
                        'bakery_featured_treats' => 'Bakery Featured Treats',
                    ]),
                    TextInput::make('content.home.espaces.eyebrow')->label('Sur-titre')->placeholder('Le chef')->maxLength(80),
                    TextInput::make('content.home.espaces.heading')->label('Titre')->placeholder('Antoine Dubois')->maxLength(160),
                    RichEditor::make('content.home.espaces.body')->label('Texte')->columnSpanFull(),
                    FilamentImageUpload::withAutomaticCompression(
                        FileUpload::make('content.home.espaces.image_url')
                            ->label('Image')
                            ->image()
                            ->disk('public')
                            ->directory('site-content/espaces')
                            ->visibility('public')
                            ->imageEditor()
                            ->columnSpanFull()
                    ),
                    TextInput::make('content.home.espaces.image_alt')->label('Texte alternatif')->maxLength(180)->columnSpanFull(),
                    Grid::make(2)
                        ->schema([
                            TextInput::make('content.home.espaces.recognition_label')->label('Libellé reconnaissance')->maxLength(80),
                            TextInput::make('content.home.espaces.recognition_value')->label('Valeur reconnaissance')->maxLength(180),
                        ]),
                    Repeater::make('content.home.espaces.links')
                        ->label('Liens')
                        ->schema([
                            TextInput::make('label')->label('Libellé')->required()->maxLength(80),
                            TextInput::make('href')->label('Lien')->required()->maxLength(255),
                        ])
                        ->minItems(0)
                        ->maxItems(8)
                        ->columns(2)
                        ->columnSpanFull(),
                ])
                ->columns(1)
                ->compact(false)),

            self::pageSection('home', 'reviews_widget', Section::make('Avis clients (TripAdvisor)')
                ->description('Section « Avis » style éditorial + intégration TripAdvisor. Laissez l’URL vide pour masquer le widget.')
                ->schema([
                    self::variantSelect('content.home.reviews_widget.variant', 'Variante visuelle', [
                        'reviews_card_deck' => 'Reviews Card Deck',
                        'reviews_quote_wall' => 'Reviews Quote Wall',
                        'reviews_split_with_widget' => 'Reviews Split With Widget',
                    ]),
                    TextInput::make('content.home.reviews_widget.section_eyebrow')->label('Petit titre au-dessus du bloc')->placeholder('Témoignages')->maxLength(80),
                    TextInput::make('content.home.reviews_widget.heading')->label('Titre de section')->placeholder('Ce que disent nos convives')->maxLength(160),
                    TextInput::make('content.home.reviews_widget.highlight_quote')->label('Citation mise en avant')->maxLength(240)->columnSpanFull(),
                    TextInput::make('content.home.reviews_widget.platform')->label('Nom de la plateforme')->maxLength(80),
                    TextInput::make('content.home.reviews_widget.platform_label_prefix')->label('Préfixe avant le nom de plateforme')->maxLength(40),
                    RichEditor::make('content.home.reviews_widget.description')->label('Description')->columnSpanFull(),
                    TextInput::make('content.home.reviews_widget.widget_helper')->label('Texte d’aide sous le nom de plateforme')->maxLength(200)->columnSpanFull(),
                    Repeater::make('content.home.reviews_widget.cards')
                        ->label('Cartes avis (bento)')
                        ->schema([
                            TextInput::make('quote')->label('Citation')->required()->maxLength(260)->columnSpanFull(),
                            TextInput::make('author')->label('Auteur')->maxLength(80),
                        ])
                        ->minItems(0)
                        ->maxItems(6)
                        ->defaultItems(0)
                        ->columnSpanFull(),
                    Grid::make(2)
                        ->schema([
                            TextInput::make('content.home.reviews_widget.url')->label('URL de la fiche')->url()->maxLength(255),
                            TextInput::make('content.home.reviews_widget.comments_link_label')->label('Libellé du lien « derniers commentaires »')->maxLength(120),
                        ]),
                    self::linkButtonsRepeater('content.home.reviews_widget.cta_buttons', 'Boutons d’action (à côté du widget)'),
                    Grid::make(2)
                        ->schema([
                            TextInput::make('content.home.reviews_widget.location_id')->label('location_id (TripAdvisor)')->maxLength(50),
                            TextInput::make('content.home.reviews_widget.widget_type')->label('Type widget « notes »')->maxLength(80),
                            TextInput::make('content.home.reviews_widget.widget_type_comments')->label('Type widget « commentaires »')->maxLength(80),
                        ]),
                ])
                ->columns(1)
                ->compact(false)),

            self::pageSection('home', 'faq', Section::make('FAQ pratique')
                ->description('Questions fréquentes affichées sur la page d’accueil.')
                ->schema([
                    self::variantSelect('content.home.faq.variant', 'Variante visuelle', [
                        'faq_accordion_cards' => 'FAQ Accordion Cards',
                        'faq_two_columns_qa' => 'FAQ Two Columns QA',
                        'faq_minimal_lines' => 'FAQ Minimal Lines',
                    ]),
                    TextInput::make('content.home.faq.heading')->label('Titre')->placeholder('FAQ')->maxLength(160),
                    TextInput::make('content.home.faq.intro')->label('Introduction')->placeholder('Régimes alimentaires, tenue, groupes : nous avons regroupé les réponses les plus courantes.')->maxLength(220)->columnSpanFull(),
                    Repeater::make('content.home.faq.items')
                        ->label('Questions / réponses')
                        ->schema([
                            TextInput::make('question')->label('Question')->required()->maxLength(200),
                            RichEditor::make('answer')->label('Réponse')->columnSpanFull(),
                        ])
                        ->minItems(0)
                        ->maxItems(12)
                        ->defaultItems(0)
                        ->columnSpanFull(),
                    Grid::make(2)
                        ->schema([
                            TextInput::make('content.home.faq.contact_label')->label('Texte du lien de contact')->maxLength(120),
                            TextInput::make('content.home.faq.contact_href')->label('Lien de contact')->maxLength(255),
                        ]),
                ])
                ->columns(1)
                ->compact(false)),

            self::pageSection('home', 'spotlight', Section::make('CTA réservation finale')
                ->description('Bandeau de fin de page avec image de fond et bouton de réservation.')
                ->schema([
                    self::variantSelect('content.home.spotlight.variant', 'Variante visuelle', [
                        'cta_full_bleed_impact' => 'CTA Full Bleed Impact',
                        'cta_split_action' => 'CTA Split Action',
                        'cta_floating_card' => 'CTA Floating Card',
                        'bakery_promo_banner' => 'Bakery Promo Banner',
                    ]),
                    TextInput::make('content.home.spotlight.heading')->label('Titre')->placeholder('Réservez votre table')->maxLength(160),
                    RichEditor::make('content.home.spotlight.body')->label('Texte')->columnSpanFull(),
                    FilamentImageUpload::withAutomaticCompression(
                        FileUpload::make('content.home.spotlight.image_url')
                            ->label('Image de fond')
                            ->image()
                            ->disk('public')
                            ->directory('site-content/spotlight')
                            ->visibility('public')
                            ->imageEditor()
                            ->columnSpanFull()
                    ),
                    self::linkButtonsRepeater('content.home.spotlight.buttons', 'Boutons'),
                ])
                ->columns(1)
                ->compact(false)),

            self::pageSection('home', 'practical', Section::make('Accès, contact & horaires')
                ->description('Infos pratiques : contact, horaires, carte Google et bouton itinéraire (adresse issue des réglages du restaurant).')
                ->schema([
                    self::variantSelect('content.home.practical.variant', 'Variante visuelle', [
                        'contact_map_dominant' => 'Contact Map Dominant',
                        'contact_info_dominant' => 'Contact Info Dominant',
                        'contact_cards_utility' => 'Contact Cards Utility',
                        'bakery_footer_contact' => 'Bakery Footer Contact',
                    ]),
                    Grid::make(2)
                        ->schema([
                            TextInput::make('content.home.practical.heading')->label('Titre de la section')->maxLength(120),
                            TextInput::make('content.home.practical.contact_title')->label('Titre colonne contact')->placeholder('Nous joindre')->maxLength(60),
                            TextInput::make('content.home.practical.opening_title')->label('Titre colonne horaires')->placeholder('Horaires d’ouverture')->maxLength(60),
                        ]),
                ])
                ->columns(1)
                ->compact(false)),
        ];
    }

    private static function paragraphsRepeater(string $path): Repeater
    {
        return Repeater::make($path)
            ->label('Paragraphes')
            ->simple(
                RichEditor::make('paragraph')
                    ->columnSpanFull()
            )
            ->minItems(1)
            ->maxItems(8)
            ->collapsible()
            ->columnSpanFull();
    }

    private static function ctaButtonsRepeater(string $path, string $label): Repeater
    {
        return Repeater::make($path)
            ->label($label)
            ->schema([
                TextInput::make('label')->label('Libellé')->required()->maxLength(80),
                TextInput::make('href')->label('Lien')->required()->maxLength(255),
                Select::make('variant')
                    ->label('Style')
                    ->options([
                        'primary' => 'Principal',
                        'secondary' => 'Secondaire',
                    ])
                    ->default('primary')
                    ->required(),
            ])
            ->minItems(0)
            ->maxItems(8)
            ->collapsible()
            ->addActionLabel('Ajouter un bouton')
            ->columnSpanFull();
    }

    private static function linkButtonsRepeater(string $path, string $label): Repeater
    {
        return Repeater::make($path)
            ->label($label)
            ->schema([
                TextInput::make('label')->label('Libellé')->required()->maxLength(120),
                TextInput::make('href')->label('Lien ou ancre')->required()->maxLength(255),
            ])
            ->minItems(0)
            ->maxItems(6)
            ->collapsible()
            ->addActionLabel('Ajouter un lien')
            ->columnSpanFull();
    }

    /**
     * @param  array<string, string>  $options
     */
    private static function variantSelect(string $path, string $label, array $options): Select
    {
        return Select::make($path)
            ->label($label)
            ->options($options)
            ->searchable()
            ->native(false)
            ->columnSpanFull();
    }

    private static function sectionOrderEditor(string $page, string $title): Section
    {
        $count = count(PageSectionCatalog::keys($page));

        return Section::make($title)
            ->description('Déplacez les lignes pour changer l’ordre des grandes sections rendues sur la page publique.')
            ->extraAttributes([
                'class' => 'hidden',
                'data-section-order-editor' => $page,
                'style' => 'display:none !important;',
                'aria-hidden' => 'true',
            ])
            ->schema([
                Repeater::make("content.{$page}.section_order")
                    ->label('')
                    ->schema([
                        Select::make('section')
                            ->label('Section')
                            ->options(PageSectionCatalog::options($page))
                            ->required(),
                    ])
                    ->reorderable()
                    ->addable(false)
                    ->deletable(false)
                    ->minItems($count)
                    ->maxItems($count)
                    ->columnSpanFull(),
            ])
            ->columns(1)
            ->compact(false);
    }

    private static function pageSection(string $page, string $sectionKey, Section $section): Section
    {
        return $section->extraAttributes([
            'data-draggable-page-section' => '1',
            'data-page' => $page,
            'data-section-key' => $sectionKey,
            'class' => 'cursor-move',
        ]);
    }

    /**
     * @return array<int, Section>
     */
    private static function carteTab(): array
    {
        return [
            self::sectionOrderEditor('carte', 'Ordre des sections de la page carte'),
            self::pageSection('carte', 'header', Section::make('En-tête de page')
                ->schema([
                    TextInput::make('content.carte.title')->label('Titre')->maxLength(160),
                    RichEditor::make('content.carte.intro')->label('Introduction')->columnSpanFull(),
                ])
                ->columns(1)
                ->compact(false)),

            self::pageSection('carte', 'menu_list', Section::make('Messages si liste vide')
                ->schema([
                    TextInput::make('content.carte.empty_state')->label('Aucune catégorie / carte vide')->maxLength(180)->columnSpanFull(),
                    TextInput::make('content.carte.empty_category_items')->label('Catégorie sans plat')->maxLength(180)->columnSpanFull(),
                ])
                ->columns(1)
                ->compact(false)),

            self::pageSection('carte', 'menu_list', Section::make('Libellés dans la liste')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('content.carte.pdf_link_label')->label('Lien PDF')->maxLength(80),
                            TextInput::make('content.carte.allergens_label')->label('Préfixe allergènes')->maxLength(80),
                        ]),
                ])
                ->columns(1)
                ->compact(false)),
        ];
    }

    /**
     * @return array<int, Section>
     */
    private static function galerieTab(): array
    {
        return [
            self::sectionOrderEditor('galerie', 'Ordre des sections de la galerie'),
            self::pageSection('galerie', 'header', Section::make('En-tête')
                ->schema([
                    TextInput::make('content.galerie.title')->label('Titre')->maxLength(160),
                    RichEditor::make('content.galerie.intro')->label('Introduction')->columnSpanFull(),
                ])
                ->columns(1)
                ->compact(false)),

            self::pageSection('galerie', 'gallery', Section::make('État vide')
                ->schema([
                    TextInput::make('content.galerie.empty_state')->label('Message si aucune photo')->maxLength(180)->columnSpanFull(),
                ])
                ->columns(1)
                ->compact(false)),

            self::pageSection('galerie', 'gallery', Section::make('Visionneuse (lightbox)')
                ->schema([
                    TextInput::make('content.galerie.lightbox_title')
                        ->label('Titre accessible de la fenêtre agrandie')
                        ->maxLength(120)
                        ->helperText('Lu par les lecteurs d’écran ; invisible à l’écran.')
                        ->columnSpanFull(),
                ])
                ->columns(1)
                ->compact(false)),
        ];
    }

    /**
     * @return array<int, Section>
     */
    private static function contactTab(): array
    {
        return [
            self::sectionOrderEditor('contact', 'Ordre des sections de la page contact'),
            self::pageSection('contact', 'header', Section::make('Page contact')
                ->schema([
                    TextInput::make('content.contact.title')->label('Titre')->maxLength(160),
                    RichEditor::make('content.contact.intro')->label('Texte sous le titre')->columnSpanFull(),
                ])
                ->columns(1)
                ->compact(false)),

            self::pageSection('contact', 'feedback', Section::make('Messages après envoi')
                ->schema([
                    RichEditor::make('content.contact.success_message')->label('Message de succès')->columnSpanFull(),
                    TextInput::make('content.contact.error_title')->label('Titre du bloc d’erreur de validation')->maxLength(120)->columnSpanFull(),
                ])
                ->columns(1)
                ->compact(false)),

            self::pageSection('contact', 'form', Section::make('Libellés du formulaire')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('content.contact.label_name')->label('Champ nom')->maxLength(40),
                            TextInput::make('content.contact.label_email')->label('Champ e-mail')->maxLength(40),
                            TextInput::make('content.contact.label_phone')->label('Champ téléphone')->maxLength(40),
                            TextInput::make('content.contact.label_phone_optional')->label('Mention « optionnel » (téléphone)')->maxLength(40),
                            TextInput::make('content.contact.label_subject')->label('Champ sujet')->maxLength(40),
                            TextInput::make('content.contact.label_message')->label('Champ message')->maxLength(40),
                            TextInput::make('content.contact.subject_placeholder')->label('Placeholder du select sujet')->maxLength(40),
                            TextInput::make('content.contact.submit_label')->label('Bouton d’envoi')->maxLength(40),
                        ]),
                ])
                ->columns(1)
                ->compact(false)),
        ];
    }

    /**
     * @return array<int, Section>
     */
    private static function reservationTab(): array
    {
        return [
            self::sectionOrderEditor('reservation', 'Ordre des sections de la page réservation'),
            self::pageSection('reservation', 'booking_form', Section::make('En-tête de page')
                ->schema([
                    TextInput::make('content.reservation.title')->label('Titre de l’onglet navigateur (optionnel)')->maxLength(120)->helperText('Utilisé dans le titre de la page ; le nom du restaurant est ajouté automatiquement.'),
                ])
                ->columns(1)
                ->compact(false)),

            self::pageSection('reservation', 'booking_form', Section::make('Accroche')
                ->schema([
                    TextInput::make('content.reservation.eyebrow')->label('Texte au-dessus du formulaire')->maxLength(120)->columnSpanFull(),
                ])
                ->columns(1)
                ->compact(false)),

            self::pageSection('reservation', 'booking_form', Section::make('Libellés des volets (accordion)')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('content.reservation.service_label')->label('Volet service')->maxLength(80),
                            TextInput::make('content.reservation.date_time_label')->label('Volet date & horaire')->maxLength(80),
                            TextInput::make('content.reservation.contact_label')->label('Volet coordonnées')->maxLength(120),
                        ]),
                ])
                ->columns(1)
                ->compact(false)),

            self::pageSection('reservation', 'booking_form', Section::make('Libellés à l’intérieur des volets')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('content.reservation.date_field_label')->label('Libellé « Date »')->maxLength(80),
                            TextInput::make('content.reservation.available_times_label')->label('Libellé « Horaires disponibles »')->maxLength(80),
                            TextInput::make('content.reservation.covers_label')->label('Libellé « Couverts »')->maxLength(80),
                            TextInput::make('content.reservation.service_placeholder')->label('Placeholder du select service')->maxLength(80),
                            TextInput::make('content.reservation.time_select_placeholder_initial')->label('Placeholder du select horaire (avant choix)')->maxLength(120),
                        ]),
                ])
                ->columns(1)
                ->compact(false)),

            self::pageSection('reservation', 'booking_form', Section::make('Placeholders des champs contact')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('content.reservation.placeholder_first_name')->label('Prénom')->maxLength(40),
                            TextInput::make('content.reservation.placeholder_last_name')->label('Nom')->maxLength(40),
                            TextInput::make('content.reservation.placeholder_email')->label('E-mail')->maxLength(40),
                            TextInput::make('content.reservation.placeholder_phone')->label('Téléphone')->maxLength(40),
                            TextInput::make('content.reservation.placeholder_notes')->label('Notes')->maxLength(80),
                        ]),
                ])
                ->columns(1)
                ->compact(false)),

            self::pageSection('reservation', 'feedback', Section::make('Messages de retour')
                ->schema([
                    RichEditor::make('content.reservation.success_message')->label('Message après envoi réussi')->columnSpanFull(),
                    TextInput::make('content.reservation.error_form_title')->label('Titre du bloc d’erreurs de validation')->maxLength(120)->columnSpanFull(),
                ])
                ->columns(1)
                ->compact(false)),

            self::pageSection('reservation', 'booking_form', Section::make('Aide & envoi')
                ->schema([
                    RichEditor::make('content.reservation.availability_help')->label('Texte d’aide sous les créneaux')->columnSpanFull(),
                    TextInput::make('content.reservation.submit_label')->label('Libellé du bouton d’envoi')->maxLength(40),
                ])
                ->columns(1)
                ->compact(false)),

            self::pageSection('reservation', 'booking_form', Section::make('Textes JavaScript (chargement des créneaux)')
                ->description('Affichés dynamiquement lors du choix de la date et du service.')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('content.reservation.js_choose_service_then_date')->label('Sans service ou date')->maxLength(120),
                            TextInput::make('content.reservation.js_loading')->label('Pendant le chargement')->maxLength(120),
                            TextInput::make('content.reservation.js_error_availability')->label('Erreur API disponibilités')->maxLength(160),
                            TextInput::make('content.reservation.js_error_network')->label('Erreur réseau')->maxLength(160),
                            TextInput::make('content.reservation.js_slot_pick')->label('Placeholder si créneaux disponibles')->maxLength(80),
                            TextInput::make('content.reservation.js_slot_none')->label('Placeholder si aucun créneau')->maxLength(80),
                            TextInput::make('content.reservation.js_slot_remaining')->label('Suffixe places (liste déroulante)')->maxLength(80),
                            TextInput::make('content.reservation.js_slot_short')->label('Abréviation places (boutons)')->maxLength(20),
                            TextInput::make('content.reservation.js_slots_count_prefix')->label('Préfixe du compteur de créneaux')->maxLength(80),
                        ]),
                ])
                ->columns(1)
                ->compact(false)),
        ];
    }

    /**
     * @return array<int, Section>
     */
    private static function reservationManageTab(): array
    {
        return [
            self::sectionOrderEditor('reservation_manage', 'Ordre des sections de la gestion de réservation'),
            self::pageSection('reservation_manage', 'header', Section::make('En-tête')
                ->schema([
                    TextInput::make('content.reservation_manage.title')->label('Titre dans la page')->maxLength(160),
                    TextInput::make('content.reservation_manage.browser_title')->label('Titre de l’onglet navigateur')->maxLength(120)->helperText('Le nom du restaurant est ajouté après un tiret.'),
                    RichEditor::make('content.reservation_manage.intro')->label('Introduction')->columnSpanFull(),
                ])
                ->columns(1)
                ->compact(false)),

            self::pageSection('reservation_manage', 'summary', Section::make('Libellés du récapitulatif')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('content.reservation_manage.details_customer_label')->label('Client')->maxLength(80),
                            TextInput::make('content.reservation_manage.details_date_label')->label('Date')->maxLength(80),
                            TextInput::make('content.reservation_manage.details_service_label')->label('Service')->maxLength(80),
                            TextInput::make('content.reservation_manage.details_covers_label')->label('Couverts')->maxLength(80),
                            TextInput::make('content.reservation_manage.details_status_label')->label('Statut')->maxLength(80),
                        ]),
                ])
                ->columns(1)
                ->compact(false)),

            self::pageSection('reservation_manage', 'actions', Section::make('Reprogrammation')
                ->schema([
                    TextInput::make('content.reservation_manage.deadline_exceeded_label')->label('Message si délai dépassé')->maxLength(180)->columnSpanFull(),
                    Grid::make(2)
                        ->schema([
                            TextInput::make('content.reservation_manage.new_date_label')->label('Champ date')->maxLength(80),
                            TextInput::make('content.reservation_manage.new_time_label')->label('Champ horaire')->maxLength(80),
                        ]),
                    RichEditor::make('content.reservation_manage.slots_help')->label('Aide sous les créneaux')->columnSpanFull(),
                    Grid::make(2)
                        ->schema([
                            TextInput::make('content.reservation_manage.reschedule_label')->label('Bouton reprogrammer')->maxLength(80),
                            TextInput::make('content.reservation_manage.cancel_label')->label('Bouton annuler')->maxLength(80),
                        ]),
                ])
                ->columns(1)
                ->compact(false)),

            self::pageSection('reservation_manage', 'actions', Section::make('Textes JavaScript (créneaux de reprogrammation)')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('content.reservation_manage.js_empty_slots')->label('Aucun créneau')->maxLength(120),
                            TextInput::make('content.reservation_manage.js_slot_places')->label('Mot « places » dans les options')->maxLength(40),
                            TextInput::make('content.reservation_manage.js_error_load')->label('Erreur de chargement')->maxLength(160),
                            TextInput::make('content.reservation_manage.js_slots_count_prefix')->label('Préfixe du compteur de créneaux')->maxLength(80),
                        ]),
                ])
                ->columns(1)
                ->compact(false)),
        ];
    }
}
