<x-mail::message>
    Votre compte a été créé. Veuillez confirmer votre e-mail.
    {{ $userData['name'] }}

    <a href="{{ $verificationUrl }}">Confirmer mon e-mail chnace</a>

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
