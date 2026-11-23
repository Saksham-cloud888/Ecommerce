<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome | {{ config('app.name', 'Ecommerce') }}</title>
</head>
<body>
    <main>
        <h1>Welcome to {{ config('app.name', 'our store') }}</h1>
        <p>Discover products from our marketplace.</p>
        <a href="{{ url('/home') }}">Continue to the store</a>
    </main>
</body>
</html>
