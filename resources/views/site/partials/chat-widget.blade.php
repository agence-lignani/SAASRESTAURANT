@if(($bistroChat['enabled'] ?? false))
    <div
        id="bistro-chat-root"
        class="bistro-chat-root"
        data-endpoint="{{ $bistroChat['endpoint'] }}"
        data-position="{{ $bistroChat['position'] }}"
        data-restaurant-id="{{ $restaurant->id }}"
    ></div>
@endif
