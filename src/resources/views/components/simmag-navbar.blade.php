<header
class="
h-20
bg-white
border-b
flex
items-center
justify-between
px-8
"
>


<div>

<h2 class="
text-2xl
font-bold
text-slate-900
">

Dashboard

</h2>


<p class="
text-sm
text-slate-500
">

Monitoring kegiatan magang

</p>


</div>




<div class="
flex
items-center
gap-5
">


<button
class="
relative
h-10
w-10
rounded-xl
hover:bg-slate-100
"
>

🔔

</button>




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


</div>


</header>