@php
    $roles = $this->getProfileRoles();
@endphp

<div class="pronote-profile-menu" role="group" aria-label="Choix du profil">
    @foreach ($roles as $key => $meta)
        <button
            type="button"
            wire:click="selectProfileAndContinue('{{ $key }}')"
            @class([
                'pronote-profile-menu-item',
                'pronote-profile-menu-item--first' => $loop->first,
            ])
            title="{{ $meta['description'] }}"
        >
            <span class="pronote-profile-menu-icon" aria-hidden="true">{{ $meta['emoji'] }}</span>
            <span class="pronote-profile-menu-label">{{ $meta['label'] }}</span>
        </button>
    @endforeach
</div>
