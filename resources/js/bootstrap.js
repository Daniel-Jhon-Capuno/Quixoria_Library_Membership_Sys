import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Laravel Echo + Pusher setup (optional — ensure packages are installed and env vars configured)
(async function() {
	try {
		const PusherModule = await import('pusher-js');
		const EchoModule = await import('laravel-echo');
		const Pusher = PusherModule.default || PusherModule;
		const Echo = EchoModule.default || EchoModule;
		window.Pusher = Pusher;
		window.Echo = new Echo({
			broadcaster: 'pusher',
			key: import.meta.env.VITE_PUSHER_APP_KEY,
			cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
			wsHost: import.meta.env.VITE_PUSHER_HOST ?? window.location.hostname,
			wsPort: import.meta.env.VITE_PUSHER_PORT ?? 6001,
			forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
			enabledTransports: ['ws', 'wss'],
			auth: {
				headers: {
					'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
				}
			}
		});
	} catch (e) {
		// Echo not available — broadcasting not configured or packages not installed
	}
})();
