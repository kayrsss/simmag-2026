<x-layouts.simmag>


<div class="space-y-8">


{{-- HEADER --}}

<div>


<h1 class="
text-3xl
font-bold
text-slate-900
">

Halo,
{{ auth()->user()?->name ?? 'Mahasiswa' }}
👋

</h1>


<p class="
mt-2
text-slate-500
">

Berikut ringkasan aktivitas magang kamu

</p>


</div>





{{-- STAT --}}

<div class="
grid
md:grid-cols-4
gap-6
">


<x-ui.stat-card

title="Status Magang"

value="Aktif"

description="Sedang berjalan"

icon="💼"

color="text-green-600"

/>



<x-ui.stat-card

title="Progress"

value="75%"

description="Tahap pelaksanaan"

icon="📊"

/>



<x-ui.stat-card

title="Logbook"

value="20"

description="Aktivitas"

icon="📚"

/>



<x-ui.stat-card

title="Dokumen"

value="3"

description="Dokumen"

icon="📄"

/>


</div>







{{-- CONTENT --}}

<div class="
grid
lg:grid-cols-3
gap-6
">





{{-- TIMELINE --}}

<div
class="
lg:col-span-2
bg-white
rounded-2xl
border
p-6
"
>


<h2 class="
text-xl
font-bold
">

Progress Magang

</h2>



<div class="mt-6 space-y-6">



<div class="flex gap-4">


<div class="
h-10
w-10
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

<p class="font-semibold">
Pengajuan Magang
</p>

<p class="text-sm text-slate-500">
Disetujui
</p>


</div>


</div>





<div class="flex gap-4">


<div class="
h-10
w-10
rounded-full
bg-blue-600
text-white
flex
items-center
justify-center
">

●

</div>


<div>

<p class="font-semibold">
Pelaksanaan
</p>

<p class="text-sm text-slate-500">
Sedang berjalan
</p>


</div>


</div>





<div class="flex gap-4">


<div class="
h-10
w-10
rounded-full
bg-slate-300
text-white
flex
items-center
justify-center
">

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







{{-- INFO --}}

<div
class="
bg-white
rounded-2xl
border
p-6
"
>


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






{{-- RECENT ACTIVITY --}}

<div
class="
bg-white
rounded-2xl
border
p-6
"
>


<h2 class="text-xl font-bold">

Aktivitas Terbaru

</h2>



<div class="mt-5 space-y-4">


<div class="flex justify-between">

<span>
Mengisi logbook hari ini
</span>


<span class="text-sm text-slate-500">
Hari ini
</span>


</div>



<div class="flex justify-between">

<span>
Upload laporan mingguan
</span>


<span class="text-sm text-slate-500">
Kemarin
</span>


</div>


</div>


</div>



</div>


</x-layouts.simmag>