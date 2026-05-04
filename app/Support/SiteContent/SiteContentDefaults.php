<?php

namespace App\Support\SiteContent;

use App\Models\Restaurant;

class SiteContentDefaults
{
    /**
     * @return array<string, mixed>
     */
    public static function forRestaurant(Restaurant $restaurant): array
    {
        $subtitle = filled($restaurant->tagline)
            ? $restaurant->tagline
            : 'Une table où l’on célèbre le marché : produits d’exception, dressages précis et une salle pensée pour la conversation. Midi comme soir, un repas qui laisse une empreinte durable.';

        $mockHeroImage = 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=1200&q=80&auto=format&fit=crop';

        return [
            'home' => [
                'section_order' => PageSectionCatalog::defaultOrder('home'),
                'hero' => [
                    'variant' => 'bakery_hero_classic',
                    'eyebrow' => 'Depuis 1985',
                    'title' => $restaurant->name,
                    'subtitle' => $subtitle,
                    'image_url' => $mockHeroImage,
                    'image_alt' => 'Ambiance de la salle et service du restaurant '.$restaurant->name,
                    'cta_buttons' => [
                        ['label' => 'Découvrir la carte', 'href' => route('site.carte'), 'variant' => 'primary'],
                        ['label' => 'Réserver une table', 'href' => route('site.reservation'), 'variant' => 'secondary'],
                    ],
                ],
                'manifesto' => [
                    'variant' => 'bakery_about_band',
                    'eyebrow' => 'Notre histoire',
                    'heading' => 'Une table ancrée dans son quartier',
                    'image_url' => 'https://images.unsplash.com/photo-1550966871-88ed8d2fb28a?w=1200&q=85&auto=format&fit=crop',
                    'image_alt' => 'Terrasse et rue piétonne devant un restaurant de quartier',
                    'paragraphs' => [
                        'Nous avons ouvert cette maison avec une idée simple : être une table de proximité — celle où l’on croise les habitués du coin, où l’on reconnaît les voix en salle, où le quartier entre dans l’assiette autant que dans la conversation.',
                        'Nos fournisseurs sont pour beaucoup à quelques kilomètres : maraîchers, fromagers, artisans du pain et du vin. Chaque saison redessine l’ardoise ; nous suivons le marché, pas la mode.',
                        'Ici, on prend le temps : un déjeuner entre collègues, un dîner sans téléphone, un verre au comptoir le vendredi. Bienvenue chez vous — à deux pas de chez vous.',
                    ],
                    'signature' => '— La maison',
                    'more_links' => [
                        ['label' => 'Découvrir la carte', 'href' => '#carte-narrative-heading'],
                    ],
                ],
                'carte_narrative' => [
                    'variant' => 'menu_featured_primary',
                    'eyebrow' => 'Carte signature',
                    'heading' => 'Une carte entre tradition de brasserie et produits du moment',
                    'image_url' => 'https://images.unsplash.com/photo-1544025162-d76694265947?w=1400&q=85&auto=format&fit=crop',
                    'image_alt' => 'Assiette gastronomique dressée, dressage soigné',
                    'paragraphs' => [
                        'Entrées généreuses, viandes et poissons au gré des arrivages, options végétariennes : nous construisons l’offre autour de ce que la saison propose de mieux.',
                        'Fromages affinés, desserts maison, carte des vins pensée pour accompagner sans imposer — demandez conseil, nous adaptons le repas à votre envie du moment.',
                    ],
                    'cta_buttons' => [
                        ['label' => 'Réserver une table', 'href' => route('site.reservation'), 'variant' => 'primary'],
                        ['label' => 'Parcourir la carte', 'href' => route('site.carte'), 'variant' => 'secondary'],
                    ],
                ],
                'menus' => [
                    'variant' => 'bakery_top_products',
                    'heading' => 'Signatures culinaires',
                    'section_title' => 'Our Pizzas',
                    'intro' => 'Une sélection des assiettes qui racontent notre cuisine : saison, précision et générosité.',
                    'items' => [
                        ['title' => 'Foie gras poêlé', 'price' => '28 €', 'description' => 'Accompagné de chutney de figues et pain d’épices.', 'image_url' => 'https://images.unsplash.com/photo-1775498017681-b95215dd704f?w=1200&q=80&auto=format&fit=crop', 'image_alt' => 'Foie gras poêlé et dressage gastronomique'],
                        ['title' => 'Turbot sauvage', 'price' => '42 €', 'description' => 'Jus de crustacés, légumes de saison rôtis.', 'image_url' => 'https://images.unsplash.com/photo-1761095596755-99ba58997720?w=1200&q=80&auto=format&fit=crop', 'image_alt' => 'Turbot dressé avec légumes de saison'],
                        ['title' => 'Soufflé au chocolat', 'price' => '16 €', 'description' => 'Glace vanille de Madagascar.', 'image_url' => 'https://images.unsplash.com/photo-1761138785146-7b5ad15851b2?w=1200&q=80&auto=format&fit=crop', 'image_alt' => 'Soufflé au chocolat et glace vanille'],
                    ],
                    'cta_buttons' => [
                        ['label' => 'Voir la carte complète', 'href' => route('site.carte'), 'variant' => 'primary'],
                    ],
                ],
                'values' => [
                    'heading' => 'Heavy on the good stuff, easy on the sweet stuff',
                    'items' => [
                        ['title' => 'Prodotti', 'text' => 'Ingrédients frais et sélectionnés pour garder l’authenticité napolitaine.'],
                        ['title' => 'Forno', 'text' => 'Four à bois pour une cuisson vive et croustillante.'],
                        ['title' => 'Tradizione', 'text' => 'Recettes transmises de génération en génération.'],
                        ['title' => 'Passione', 'text' => 'Chaque pizza est montée avec précision et caractère.'],
                    ],
                ],
                'gallery_highlights' => [
                    'variant' => 'bakery_explore_grid',
                    'heading' => 'L’esprit & la Maison',
                    'intro' => 'Salle principale, cave, cuisine ouverte et salon : chaque espace participe à l’expérience.',
                    'items' => [
                        [
                            'image_url' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=1600&q=80&auto=format&fit=crop',
                            'image_alt' => 'Salle du restaurant, éclairage tamisé et tables dressées',
                            'caption' => 'La salle principale',
                        ],
                        [
                            'image_url' => 'https://images.unsplash.com/photo-1553361371-9b22f78e8b1d?w=900&q=80&auto=format&fit=crop',
                            'image_alt' => 'Cave à vin avec bouteilles en lumière douce',
                            'caption' => 'La réserve',
                        ],
                        [
                            'image_url' => 'https://images.unsplash.com/photo-1556910103-1c02745aae4d?w=1200&q=80&auto=format&fit=crop',
                            'image_alt' => 'Préparation en cuisine sur plan de travail',
                            'caption' => '',
                        ],
                        [
                            'image_url' => '',
                            'image_alt' => 'Un espace intimiste pour prolonger la soirée autour d’un digestif ou d’un accord mets-vins.',
                            'caption' => 'Le salon',
                        ],
                    ],
                    'gallery_link_label' => 'Parcourir toute la galerie',
                    'gallery_link_href' => route('site.galerie'),
                    'mosaic_title' => 'Ambiances de la maison',
                ],
                'practical' => [
                    'variant' => 'bakery_footer_contact',
                    'heading' => 'Venir au restaurant',
                    'contact_title' => 'Nous joindre',
                    'opening_title' => 'Horaires d’ouverture',
                    'opening_lines' => [],
                    'footer_address_title' => 'Adresse',
                    'footer_contact_title' => 'Contact',
                    'footer_hours_title' => 'Horaires',
                    'footer_legal_line' => 'Palazzo Pizzeria — Tous droits réservés.',
                ],
            ],
            'carte' => [
                'section_order' => PageSectionCatalog::defaultOrder('carte'),
                'title' => 'Notre carte',
                'intro' => 'Prix et disponibilités donnés à titre indicatif ; merci de vous adresser à la salle pour toute allergie ou variante du jour.',
                'empty_state' => 'La carte sera bientôt en ligne.',
                'pdf_link_label' => 'Télécharger le PDF',
                'empty_category_items' => 'Aucun plat dans cette catégorie pour le moment.',
                'allergens_label' => 'Allergènes :',
            ],
            'contact' => [
                'section_order' => PageSectionCatalog::defaultOrder('contact'),
                'title' => 'Nous écrire',
                'intro' => 'Réservation, privatisation ou simple message : nous vous répondrons dès que possible.',
                'success_message' => 'Merci, votre message a bien été envoyé.',
                'error_title' => 'Veuillez corriger les champs ci-dessous.',
                'label_name' => 'Nom',
                'label_email' => 'E-mail',
                'label_phone' => 'Téléphone',
                'label_phone_optional' => '(optionnel)',
                'label_subject' => 'Sujet',
                'label_message' => 'Message',
                'subject_placeholder' => 'Choisir…',
                'submit_label' => 'Envoyer',
            ],
            'reservation' => [
                'section_order' => PageSectionCatalog::defaultOrder('reservation'),
                'title' => 'Réservation',
                'eyebrow' => 'Choisissez vos préférences',
                'service_label' => 'Service',
                'date_time_label' => 'Date et horaire',
                'contact_label' => 'Informations client',
                'date_field_label' => 'Date',
                'available_times_label' => 'Horaires disponibles',
                'covers_label' => 'Couverts',
                'service_placeholder' => 'Choisir un service',
                'placeholder_first_name' => 'Prénom',
                'placeholder_last_name' => 'Nom',
                'placeholder_email' => 'E-mail',
                'placeholder_phone' => 'Téléphone',
                'placeholder_notes' => 'Notes (optionnel)',
                'success_message' => 'Merci ! Votre demande de réservation a bien été enregistrée.',
                'error_form_title' => 'Le formulaire contient des erreurs :',
                'availability_help' => 'Les horaires proposés sont synchronisés avec les réservations enregistrées.',
                'submit_label' => 'Réserver',
                'time_select_placeholder_initial' => 'Choisir d’abord une date et un service',
                'js_choose_service_then_date' => 'Choisissez un service puis une date.',
                'js_loading' => 'Chargement des disponibilités...',
                'js_error_availability' => 'Impossible de charger les disponibilités pour le moment.',
                'js_error_network' => 'Erreur réseau lors du chargement des disponibilités.',
                'js_slot_pick' => 'Choisir un horaire',
                'js_slot_none' => 'Aucun horaire disponible',
                'js_slot_remaining' => 'place(s) restantes',
                'js_slot_short' => 'pl.',
                'js_slots_count_prefix' => 'Créneaux disponibles :',
            ],
            'reservation_manage' => [
                'section_order' => PageSectionCatalog::defaultOrder('reservation_manage'),
                'title' => 'Gérer ma réservation',
                'browser_title' => 'Gérer ma réservation',
                'intro' => 'Vous pouvez annuler ou choisir un autre créneau selon les disponibilités.',
                'details_customer_label' => 'Client :',
                'details_date_label' => 'Date actuelle :',
                'details_service_label' => 'Service :',
                'details_covers_label' => 'Couverts :',
                'details_status_label' => 'Statut :',
                'deadline_exceeded_label' => 'Le délai pour annuler ou reprogrammer est dépassé.',
                'new_date_label' => 'Nouvelle date',
                'new_time_label' => 'Nouvel horaire',
                'slots_help' => 'Les créneaux proposés tiennent compte des disponibilités en temps réel.',
                'reschedule_label' => 'Reprogrammer',
                'cancel_label' => 'Annuler ma réservation',
                'js_empty_slots' => 'Aucun créneau disponible',
                'js_slot_places' => 'places',
                'js_error_load' => 'Erreur lors du chargement des disponibilités.',
                'js_slots_count_prefix' => 'Créneaux disponibles :',
            ],
            'galerie' => [
                'section_order' => PageSectionCatalog::defaultOrder('galerie'),
                'title' => 'Galerie',
                'intro' => 'Quelques images de notre maison. Cliquez sur une photo pour l’agrandir.',
                'empty_state' => 'Les photos seront bientôt disponibles.',
                'lightbox_title' => 'Visionneuse de la galerie',
            ],
        ];
    }
}
