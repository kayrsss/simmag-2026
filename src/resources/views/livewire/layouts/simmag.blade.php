<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <title>
        SIMMAG
    </title>


    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])


</head>


<body
class="
bg-slate-50
text-slate-900
"
>


<div
class="
flex
min-h-screen
"
>


    {{-- SIDEBAR --}}

    <aside
    class="
    w-72
    bg-white
    border-r
    fixed
    h-screen
    "
    >


        <div
        class="
        px-8
        py-8
        "
        >


            <img
            src="{{ asset('images/logo-simmag.png') }}"
            class="
            w-44
            "
            >


        </div>



        <nav
        class="
        px-5
        space-y-2
        "
        >


            <a
            href="/dashboard"
            class="
            flex
            gap-4
            items-center
            px-5
            py-3
            rounded-xl
            bg-blue-600
            text-white
            font-semibold
            "
            >

                🏠
                Dashboard

            </a>



            <a
            href="#"
            class="
            flex
            gap-4
            px-5
            py-3
            rounded-xl
            text-slate-600
            hover:bg-blue-50
            "
            >

                💼
                Magang

            </a>



            <a
            href="#"
            class="
            flex
            gap-4
            px-5
            py-3
            rounded-xl
            text-slate-600
            hover:bg-blue-50
            "
            >

                📚
                Logbook

            </a>




            <a
            href="#"
            class="
            flex
            gap-4
            px-5
            py-3
            rounded-xl
            text-slate-600
            hover:bg-blue-50
            "
            >

                📄
                Dokumen

            </a>




            <a
            href="#"
            class="
            flex
            gap-4
            px-5
            py-3
            rounded-xl
            text-slate-600
            hover:bg-blue-50
            "
            >

                💬
                Konsultasi

            </a>


        </nav>



    </aside>






    {{-- MAIN --}}


    <main
    class="
    ml-72
    flex-1
    "
    >



        {{-- HEADER --}}

        <header
        class="
        h-20
        bg-white
        border-b
        flex
        items-center
        justify-between
        px-10
        "
        >


            <div>


                <h1
                class="
                text-2xl
                font-bold
                "
                >

                Dashboard

                </h1>


                <p
                class="
                text-slate-500
                "
                >

                Sistem Informasi Monitoring Magang

                </p>


            </div>




            <div
            class="
            flex
            items-center
            gap-5
            "
            >


                🔔



                <div
                class="
                w-11
                h-11
                rounded-full
                bg-blue-600
                text-white
                flex
                items-center
                justify-center
                font-bold
                "
                >

                {{ strtoupper(substr(auth()->user()->name ?? 'U',0,1)) }}

                </div>


            </div>


        </header>




        {{-- CONTENT --}}


        <section
        class="
        p-10
        "
        >


            {{ $slot }}


        </section>



    </main>



</div>



</body>

</html>