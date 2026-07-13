<div
class="
bg-white
rounded-2xl
border
p-6
shadow-sm
"
>


<div class="flex items-center justify-between">


<div>


<p class="text-sm text-slate-500">
{{ $title }}
</p>


<h2 class="
mt-3
text-3xl
font-bold
text-slate-900
">
{{ $value }}
</h2>


<p class="
mt-2
text-sm
{{ $color ?? 'text-blue-600' }}
">

{{ $description }}

</p>


</div>


<div
class="
h-12
w-12
rounded-xl
bg-blue-50
flex
items-center
justify-center
text-xl
"
>

{{ $icon }}

</div>


</div>


</div>