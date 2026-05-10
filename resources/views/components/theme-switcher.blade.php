@props(['current' => 'dark'])

<div class="relative flex items-center gap-0 p-1 rounded-full border-2 theme-switcher-container transition-all duration-300 hover:shadow-lg" 
     :style="{ 
         borderColor: 'rgb(var(--border-primary))',
         backgroundColor: 'rgb(var(--surface-primary) / 0.8)'
     }">
    
    <!-- Animated Slider Background -->
    <div class="absolute inset-0 rounded-full transition-all duration-500 ease-out"
         :style="{
             backgroundColor: isDarkMode ? 'rgba(100, 200, 255, 0.2)' : 'rgba(59, 130, 246, 0.2)',
             transform: isDarkMode ? 'translateX(0)' : 'translateX(calc(100% - 4px))',
             zIndex: 0
         }"
         @class="isDarkMode ? 'drop-shadow-md' : 'drop-shadow-md'">
    </div>
    
    <!-- Dark Mode Button -->
    <button onclick="setTheme('dark')" 
            class="relative flex items-center justify-center gap-2 px-4 py-2 rounded-full font-medium transition-all duration-300 z-10 min-w-[80px]"
            :class="isDarkMode ? 'text-white font-bold' : 'text-slate-500 hover:text-slate-400'"
            title="Dark Theme">
        <svg class="w-5 h-5 transition-transform duration-300" :class="isDarkMode ? 'scale-110' : 'scale-90'" fill="currentColor" viewBox="0 0 20 20">
            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
        </svg>
        <span class="hidden sm:inline text-sm">Dark</span>
    </button>

    <!-- Light Mode Button -->
    <button onclick="setTheme('light')" 
            class="relative flex items-center justify-center gap-2 px-4 py-2 rounded-full font-medium transition-all duration-300 z-10 min-w-[80px]"
            :class="!isDarkMode ? 'text-white font-bold' : 'text-slate-500 hover:text-slate-400'"
            title="Light Theme">
        <svg class="w-5 h-5 transition-transform duration-300" :class="!isDarkMode ? 'scale-110' : 'scale-90'" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l-2.12-2.12a1 1 0 00-1.414 1.414l2.12 2.12a1 1 0 001.414-1.414zM2.05 6.464a1 1 0 00-1.414 1.414l2.12 2.12a1 1 0 001.414-1.414L2.05 6.464zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.64 7.464a1 1 0 00-1.414-1.414L2.12 8.05a1 1 0 001.414 1.414l2.12-2.12zm12.72 0l-2.12 2.12a1 1 0 001.414 1.414l2.12-2.12a1 1 0 00-1.414-1.414zM1 11a1 1 0 100-2h-1a1 1 0 100 2h1z" clip-rule="evenodd"></path>
        </svg>
        <span class="hidden sm:inline text-sm">Light</span>
    </button>
</div>

<style>
@keyframes dropIn {
    0% {
        opacity: 0;
        transform: translateY(-20px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

.theme-switcher-container {
    animation: dropIn 0.4s ease-out;
}
</style>

<script>
    // Check initial theme preference
    function initTheme() {
        const savedTheme = localStorage.getItem('library-theme') || 'dark';
        setTheme(savedTheme);
    }

    function setTheme(theme) {
        const html = document.documentElement;
        const container = document.querySelector('.theme-switcher-container');
        
        // Trigger drop animation
        if (container) {
            container.style.animation = 'none';
            setTimeout(() => {
                container.style.animation = 'dropIn 0.3s ease-out';
            }, 10);
        }
        
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
        
        document.body.dispatchEvent(new CustomEvent('theme-changed', { detail: theme, bubbles: true }));

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
@keyframes dropIn {
    0% {
        opacity: 0;
        transform: translateY(-20px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

.theme-switcher-container {
    background: rgb(var(--surface-primary) / 0.8);
    border-color: rgb(var(--border-primary));
}

.theme-switcher-container:hover {
    box-shadow: 0 4px 12px rgba(100, 200, 255, 0.15);
}

.theme-switcher-container button {
    background-color: transparent;
    transition: all 0.3s ease;
}

.theme-switcher-container button:hover {
    color: rgb(var(--text-primary));
}

/* Indicator slider animation on transition */
.theme-switcher-container > div:first-of-type {
    transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
}
</style>
