@props(['current' => 'dark'])

<div class="flex items-center gap-2 p-2 rounded-lg border" 
     :style="{ 
         borderColor: 'rgb(var(--border-primary))',
         backgroundColor: 'rgb(var(--surface-primary) / 0.5)'
     }">
    
    <!-- Dark Mode Button -->
    <button onclick="setTheme('dark')" 
            class="flex items-center gap-2 px-3 py-2 rounded-lg font-medium transition-all duration-200"
            :class="isDarkMode ? 'bg-primary text-white shadow-lg' : 'text-slate-400 hover:text-slate-200'"
            title="Dark Theme">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
        </svg>
        <span class="hidden sm:inline text-sm">Dark</span>
    </button>

    <!-- Light Mode Button -->
    <button onclick="setTheme('light')" 
            class="flex items-center gap-2 px-3 py-2 rounded-lg font-medium transition-all duration-200"
            :class="!isDarkMode ? 'bg-primary text-white shadow-lg' : 'text-slate-400 hover:text-slate-200'"
            title="Light Theme">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l-2.12-2.12a1 1 0 00-1.414 1.414l2.12 2.12a1 1 0 001.414-1.414zM2.05 6.464a1 1 0 00-1.414 1.414l2.12 2.12a1 1 0 001.414-1.414L2.05 6.464zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.64 7.464a1 1 0 00-1.414-1.414L2.12 8.05a1 1 0 001.414 1.414l2.12-2.12zm12.72 0l-2.12 2.12a1 1 0 001.414 1.414l2.12-2.12a1 1 0 00-1.414-1.414zM1 11a1 1 0 100-2h-1a1 1 0 100 2h1z" clip-rule="evenodd"></path>
        </svg>
        <span class="hidden sm:inline text-sm">Light</span>
    </button>
</div>

<script>
    // Check initial theme preference
    function initTheme() {
        const savedTheme = localStorage.getItem('library-theme') || 'dark';
        setTheme(savedTheme);
    }

    function setTheme(theme) {
        const html = document.documentElement;
        
        if (theme === 'light') {
            html.classList.add('light-mode');
            html.classList.remove('dark');
            localStorage.setItem('library-theme', 'light');
            updateChartColors('light');
        } else {
            html.classList.remove('light-mode');
            html.classList.add('dark');
            localStorage.setItem('library-theme', 'dark');
            updateChartColors('dark');
        }
        
        // Trigger Alpine reactivity
        if (window.Alpine) {
            window.Alpine.store('theme', {
                isDark: theme === 'dark'
            });
        }
        
        // Redraw charts with new colors
        setTimeout(() => {
            if (window.charts) {
                Object.values(window.charts).forEach(chart => {
                    if (chart && typeof chart.update === 'function') {
                        chart.destroy && chart.destroy();
                    }
                });
                // Notify that charts need to be redrawn
                window.dispatchEvent(new Event('themeChanged'));
            }
        }, 100);
    }

    function updateChartColors(theme) {
        if (!window.Chart) return;
        
        const isDark = theme === 'dark';
        const gridColor = isDark ? 'rgba(100, 116, 139, 0.1)' : 'rgba(209, 213, 219, 0.3)';
        const tickColor = isDark ? '#94a3b8' : '#6b7280';
        const legendColor = isDark ? '#cbd5e1' : '#374151';
        
        // Store for chart initialization
        window.chartDefaults = {
            gridColor,
            tickColor,
            legendColor,
            isDark
        };
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', initTheme);
</script>

<style>
    button {
        background-color: rgb(var(--surface-secondary));
        color: rgb(var(--text-secondary));
    }

    button:hover {
        color: rgb(var(--text-primary));
    }
</style>
