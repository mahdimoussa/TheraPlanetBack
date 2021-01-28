@component('mail::message')
    <strong>Thera Planet</strong> account password reset


@component('mail::button', ['url' => 'http://localhost:4200/resetpassword?token='.$token])
Click Here
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
