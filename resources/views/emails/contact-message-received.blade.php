<x-mail::message>
# Nouveau message depuis le site

**De :** {{ $message->name }} ({{ $message->email }})  
@if(filled($message->phone))
**Téléphone :** {{ $message->phone }}  
@endif
**Sujet :** {{ \App\Models\ContactMessage::subjectOptions()[$message->subject] ?? $message->subject }}

---

{!! nl2br(e($message->body)) !!}

---

<x-mail::panel>
Établissement : {{ $restaurant->name }} — IP : {{ $message->ip_address ?? '—' }}
</x-mail::panel>
</x-mail::message>
