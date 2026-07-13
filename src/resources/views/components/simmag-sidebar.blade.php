<aside
    class="
        w-72
        min-h-screen
        bg-white
        border-r
        flex
        flex-col
    "
>


    {{-- LOGO --}}
    <div class="px-8 py-7">

        <div class="flex items-center gap-3">


            <div
                class="
                    w-11
                    h-11
                    rounded-xl
                    bg-blue-600
                    flex
                    items-center
                    justify-center
                    text-white
                    font-bold
                    text-xl
                "
            >
                S
            </div>


            <div>

                <h1
                    class="
                        text-2xl
                        font-bold
                        text-slate-900
                    "
                >
                    SIMMAG
                </h1>


                <p
                    class="
                        text-xs
                        text-slate-500
                    "
                >
                    Sistem Informasi Magang
                </p>

            </div>


        </div>


    </div>





    {{-- MENU --}}
    <nav
        class="
            flex-1
            px-5
            space-y-2
        "
    >


        {{-- DASHBOARD --}}
        <a
            href="/dashboard"
            class="
                flex
                items-center
                gap-4
                px-5
                py-3
                rounded-xl
                bg-blue-600
                text-white
                font-medium
            "
        >

            <span class="text-lg">
                🏠
            </span>

            Dashboard

        </a>





        {{-- MAGANG --}}
        <a
            href="/magang"
            class="
                flex
                items-center
                gap-4
                px-5
                py-3
                rounded-xl
                text-slate-600
                hover:bg-blue-50
                hover:text-blue-600
                transition
            "
        >

            <span>
                💼
            </span>

            Magang

        </a>





        {{-- DOKUMEN --}}
        <a
            href="/dokumen"
            class="
                flex
                items-center
                gap-4
                px-5
                py-3
                rounded-xl
                text-slate-600
                hover:bg-blue-50
                hover:text-blue-600
                transition
            "
        >

            <span>
                📄
            </span>

            Dokumen

        </a>





        {{-- LOGBOOK --}}
        <a
            href="/logbook"
            class="
                flex
                items-center
                gap-4
                px-5
                py-3
                rounded-xl
                text-slate-600
                hover:bg-blue-50
                hover:text-blue-600
                transition
            "
        >

            <span>
                📚
            </span>

            Logbook

        </a>





        {{-- KONSULTASI --}}
        <a
            href="/konsultasi"
            class="
                flex
                items-center
                gap-4
                px-5
                py-3
                rounded-xl
                text-slate-600
                hover:bg-blue-50
                hover:text-blue-600
                transition
            "
        >

            <span>
                💬
            </span>

            Konsultasi

        </a>


    </nav>







    {{-- USER --}}
    <div
        class="
            border-t
            p-6
        "
    >


        <div
            class="
                flex
                items-center
                gap-3
            "
        >


            <div
                class="
                    h-12
                    w-12
                    rounded-full
                    bg-blue-600
                    text-white
                    flex
                    items-center
                    justify-center
                    font-bold
                "
            >

                {{
                    strtoupper(
                        substr(
                            auth()->user()?->name ?? 'G',
                            0,
                            1
                        )
                    )
                }}

            </div>




            <div>


                <p
                    class="
                        font-semibold
                        text-slate-900
                    "
                >

                    {{ auth()->user()?->name ?? 'Guest' }}

                </p>



                <p
                    class="
                        text-xs
                        text-slate-500
                    "
                >

                    {{ auth()->user()?->email ?? '' }}

                </p>


            </div>


        </div>


    </div>


</aside>