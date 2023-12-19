<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title') - {{ env('APP_NAME') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <style>
        body {
            background: #212529;
            color: rgb(222, 226, 230);
        }

        .header{
            margin: 30px 0;
        }

        .main{
            background: rgba(0,0,0,0.3);
            border-radius: 10px;
            padding: 20px;
        }
    </style>
</head>
<body class="container">

@include('shared.header')

<main class="main">
    <h1 class="mb-4">@yield('title')</h1>
    @include('shared.flash')
    @yield('content')
</main>

@include('shared.footer')

</body>
</html>
