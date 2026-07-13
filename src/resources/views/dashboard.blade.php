<x-layouts.simmag>

<div class="space-y-8">


    {{-- HEADER --}}
    <div class="flex justify-between items-center">


        <div>

            <h1 class="text-3xl font-bold text-slate-900">
                Halo, {{ auth()->user()?->name ?? 'Mahasiswa' }} 👋
            </h1>


            <p class="mt-2 text-slate-500">
                Selamat datang di dashboard SIMMAG
            </p>

        </div>


        <div class="
            rounded-xl
            bg-blue-100
            px-5
            py-3
            text-blue-700
            font-semibold
        ">

            Mahasiswa

        </div>


    </div>





    {{-- STAT CARD --}}
    <div class="grid md:grid-cols-3 gap-6">


        <div class="
            bg-white
            rounded-2xl
            border
            p-6
            shadow-sm
        ">


            <p class="text-sm text-slate-500">
                Status Magang
            </p>


            <h2 class="text-3xl font-bold mt-3">
                Aktif
            </h2>


            <span class="text-green-600 text-sm">
                ● Sedang Berjalan
            </span>


        </div>





        <div class="
            bg-white
            rounded-2xl
            border
            p-6
            shadow-sm
        ">


            <p class="text-sm text-slate-500">
                Progress Magang
            </p>


            <h2 class="text-3xl font-bold mt-3">
                75%
            </h2>



            <div class="mt-4 h-2 bg-slate-200 rounded-full">

                <div
                    class="
                    h-2
                    w-3/4
                    bg-blue-600
                    rounded-full
                    "
                ></div>


            </div>


        </div>





        <div class="
            bg-white
            rounded-2xl
            border
            p-6
            shadow-sm
        ">


            <p class="text-sm text-slate-500">
                Total Logbook
            </p>


            <h2 class="text-3xl font-bold mt-3">
                20
            </h2>


            <span class="text-blue-600 text-sm">
                Aktivitas tercatat
            </span>


        </div>


    </div>







    {{-- CONTENT --}}
    <div class="grid lg:grid-cols-3 gap-6">



        {{-- TIMELINE --}}

        <div class="
            lg:col-span-2
            bg-white
            rounded-2xl
            border
            p-6
        ">


            <h2 class="text-xl font-bold">
                Timeline Magang
            </h2>



            <div class="mt-6 space-y-6">


                <div class="flex gap-4">

                    <div class="
                        w-10
                        h-10
                        rounded-full
                        bg-green-500
                        text-white
                        flex
                        items-center
                        justify-center
                    ">
                        ✓
                    </div>


                    <div>

                        <h3 class="font-semibold">
                            Pengajuan Magang
                        </h3>


                        <p class="text-sm text-slate-500">
                            Disetujui
                        </p>


                    </div>


                </div>





                <div class="flex gap-4">


                    <div class="
                        w-10
                        h-10
                        rounded-full
                        bg-blue-600
                        text-white
                        flex
                        items-center
                        justify-center
                    ">
                        2
                    </div>


                    <div>

                        <h3 class="font-semibold">
                            Pelaksanaan Magang
                        </h3>


                        <p class="text-sm text-slate-500">
                            Sedang berlangsung
                        </p>


                    </div>


                </div>





                <div class="flex gap-4">


                    <div class="
                        w-10
                        h-10
                        rounded-full
                        bg-slate-300
                        text-white
                        flex
                        items-center
                        justify-center
                    ">
                        3
                    </div>


                    <div>

                        <h3 class="font-semibold">
                            Laporan Akhir
                        </h3>


                        <p class="text-sm text-slate-500">
                            Menunggu
                        </p>


                    </div>


                </div>



            </div>


        </div>







        {{-- INFO MAGANG --}}

        <div class="
            bg-white
            rounded-2xl
            border
            p-6
        ">


            <h2 class="text-xl font-bold">
                Informasi Magang
            </h2>



            <div class="mt-5 space-y-5">


                <div>

                    <p class="text-sm text-slate-500">
                        Perusahaan
                    </p>


                    <p class="font-semibold">
                        PT Contoh Indonesia
                    </p>


                </div>




                <div>

                    <p class="text-sm text-slate-500">
                        Pembimbing
                    </p>


                    <p class="font-semibold">
                        Dosen Pembimbing
                    </p>


                </div>




                <div>

                    <p class="text-sm text-slate-500">
                        Periode
                    </p>


                    <p class="font-semibold">
                        2026
                    </p>


                </div>


            </div>


        </div>



    </div>



</div>


</x-layouts.simmag>