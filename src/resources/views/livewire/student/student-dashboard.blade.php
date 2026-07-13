<div class="space-y-8">


    {{-- HEADER --}}
    <div>

        <h1 class="text-3xl font-bold text-slate-900">
            Selamat Datang,
            {{ auth()->user()->name ?? 'Mahasiswa' }} 👋
        </h1>


        <p class="mt-2 text-slate-500">
            Pantau aktivitas magang kamu melalui SIMMAG
        </p>

    </div>




    {{-- SUMMARY CARD --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">


        <div class="bg-white rounded-3xl p-6 shadow-sm border">

            <p class="text-sm text-slate-500">
                Status Magang
            </p>


            <h2 class="mt-3 text-3xl font-bold">

                {{ ucfirst($this->internship?->status ?? 'Belum Ada') }}

            </h2>


            <p class="mt-2 text-slate-500">
                Berjalan
            </p>

        </div>





        <div class="bg-white rounded-3xl p-6 shadow-sm border">

            <p class="text-sm text-slate-500">
                Progress
            </p>


            <h2 class="mt-3 text-3xl font-bold">

                {{ $progress }}%

            </h2>


            <div class="mt-4 bg-slate-200 rounded-full h-3">

                <div
                    class="bg-blue-600 h-3 rounded-full"
                    style="width: {{ $progress }}%"
                ></div>

            </div>


        </div>





        <div class="bg-white rounded-3xl p-6 shadow-sm border">

            <p class="text-sm text-slate-500">
                Total Logbook
            </p>


            <h2 class="mt-3 text-3xl font-bold">

                {{ $totalLogbook }}

            </h2>


            <p class="mt-2 text-slate-500">
                Aktivitas tercatat
            </p>


        </div>


    </div>





    {{-- TIMELINE --}}

    <div class="bg-white rounded-3xl shadow-sm border p-8">


        <h2 class="text-xl font-bold mb-6">
            Timeline Magang
        </h2>



        <div class="space-y-6">


            <div class="flex gap-4">

                <div class="text-green-600 text-xl">
                    ✓
                </div>

                <div>
                    <p class="font-semibold">
                        Pengajuan Magang
                    </p>

                    <p class="text-sm text-slate-500">
                        Data pengajuan berhasil dibuat
                    </p>
                </div>

            </div>





            <div class="flex gap-4">

                <div class="text-green-600 text-xl">
                    ✓
                </div>


                <div>

                    <p class="font-semibold">
                        Kerangka Acuan
                    </p>

                    <p class="text-sm text-slate-500">
                        Menunggu proses berikutnya
                    </p>

                </div>


            </div>





            <div class="flex gap-4">

                <div class="text-blue-600 text-xl">
                    ●
                </div>


                <div>

                    <p class="font-semibold">
                        Pelaksanaan Magang
                    </p>

                    <p class="text-sm text-slate-500">
                        Sedang berlangsung
                    </p>

                </div>


            </div>





            <div class="flex gap-4">


                <div class="text-slate-400 text-xl">
                    ○
                </div>


                <div>

                    <p class="font-semibold">
                        Laporan Akhir
                    </p>

                    <p class="text-sm text-slate-500">
                        Belum selesai
                    </p>

                </div>


            </div>


        </div>


    </div>




</div>