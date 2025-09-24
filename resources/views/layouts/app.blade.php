<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @php
        // Read the manifest file
        $manifestPath = public_path('vendor/devguard/manifest.json');
        $manifest = [];
        
        if (file_exists($manifestPath)) {
            $manifest = json_decode(file_get_contents($manifestPath), true);
        }
        
        // Get the main entry file
        $mainEntry = $manifest['resources/js/app.tsx'] ?? null;
    @endphp

    @if($mainEntry)
        <!-- CSS files -->
        @if(isset($mainEntry['css']))
            @foreach($mainEntry['css'] as $cssFile)
                <link rel="stylesheet" href="{{ asset('vendor/devguard/' . $cssFile) }}">
            @endforeach
        @endif
    @else
        <!-- Fallback CSS -->
        <link rel="stylesheet" href="{{ asset('vendor/devguard/assets/app.css') }}">
    @endif

    @routes()
    @inertiaHead
</head>

<body class="font-sans antialiased">
    @inertia
    
    @if($mainEntry)
        <!-- Main JS file -->
        <script src="{{ asset('vendor/devguard/' . $mainEntry['file']) }}" defer></script>
    @else
        <!-- Fallback JS -->
        <script src="{{ asset('vendor/devguard/assets/app.js') }}" defer></script>
    @endif
</body>

</html>