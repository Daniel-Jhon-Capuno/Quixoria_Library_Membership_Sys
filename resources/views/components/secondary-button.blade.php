<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-slate-700 border border-slate-600 rounded-md font-semibold text-xs text-gray-100 uppercase tracking-widest shadow-sm hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
