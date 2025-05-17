<link rel="stylesheet" href="{{ asset('themes/default/assets/css/admin.css') }}">
{{-- packages/Wontonee/Shiprocket/src/Resources/views/admin/layouts/style.blade.php --}}
@php
    $manifestPath = public_path('themes/shiprocket/default/build/manifest.json');
    $viteCss = null;

    if (file_exists($manifestPath)) {
        $manifest = json_decode(file_get_contents($manifestPath), true);

        $viteCssKey = 'src/Resources/assets/css/app.css';
        $viteCss = $manifest[$viteCssKey]['file'] ?? null;

      //  dd($viteCss);
    }
@endphp

@if ($viteCss)
    <link rel="stylesheet" href="{{ asset('themes/shiprocket/default/build/' . $viteCss) }}">
    @else
        <link rel="stylesheet" href="{{ asset('themes/shiprocket/default/build/css/app.css') }}">
@endif