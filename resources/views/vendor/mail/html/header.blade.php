@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
{{-- PNG, not SVG: most mail clients do not render SVG. See docs/BRAND.md. --}}
<img src="{{ asset('brand/qrx-logo.png') }}" width="116" height="48" alt="{{ config('app.name') }}" style="height: 48px; width: auto;">
</a>
</td>
</tr>
