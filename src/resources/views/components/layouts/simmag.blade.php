<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>SIMMAG</title>


    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

</head>


<body class="bg-slate-100">


<div class="flex min-h-screen">


    {{-- SIDEBAR --}}
    <x-simmag-sidebar />



    {{-- MAIN AREA --}}
    <div class="flex-1 flex flex-col">


        {{-- NAVBAR --}}
        <x-simmag-navbar />



        {{-- CONTENT --}}
        <main class="p-8">

            {{ $slot }}

        </main>


    </div>


</div>


</body>

</html>