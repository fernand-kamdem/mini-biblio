@props(['url'])
{{-- En-tête des e-mails : logo et nom de l'application, liés au site.
     Le logo est décoratif (alt vide) : si les images sont bloquées, le nom reste affiché.
     Pas d'indentation ici : en Markdown, une ligne indentée deviendrait un bloc de code. --}}
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
<img src="{{ asset('images/logo.png') }}" class="logo" width="40" height="40" alt="">
{!! $slot !!}
</a>
</td>
</tr>
