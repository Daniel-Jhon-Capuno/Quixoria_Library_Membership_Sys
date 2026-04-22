@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-slate-900 text-gray-100 border border-slate-700 focus:border-cyan-400 focus:ring-cyan-400 rounded-md shadow-sm']) }}>
