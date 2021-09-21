<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/all.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">

     <link type="text/css" rel="stylesheet" href="{{ asset('css/style.css') }}">
    <title>Grant Cardone</title>
</head>

<body class="bg-main">
    <div id="app" class="w-100 h-100">
            @yield('content')
            
    </div>
</body>
<script src="{{asset('js/app.js')}}"></script>
</html>