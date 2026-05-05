<!doctype html>
<html lang="en" class="dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ config('app.name') }} — Library Membership System</title>
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&family=Space+Grotesk:wght@400;500;700;800&display=swap" rel="stylesheet" />
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased mesh-gradient relative overflow-x-hidden">&#10;  <div class="particles"></div>
  <header class="w-full py-6 px-6 bg-primary text-white">
    <div class="max-w-6xl mx-auto flex items-center justify-between">
      <div class="flex items-center gap-3">
        <img src="/images/logo.png" alt="Quixoria" class="w-16 h-16 md:w-20 md:h-20 rounded-xl bg-white/20 shadow-2xl object-contain border border-white/20 hover:scale-110 transition-all duration-300">
        <div>
          <h1 class="text-xl font-black font-[space_grotesk] hero-gradient">{{ config('app.name', 'Quixoria') }}</h1>
          <p class="text-sm text-white/80">Start your reading adventure here with Quixoria</p>
        </div>
      </div>
      <nav class="flex items-center gap-4">
        <a href="{{ route('login') }}" class="px-4 py-2 rounded-md text-white/90 hover:opacity-90">Sign In</a>
        <a href="{{ route('register') }}" class="btn-primary">Try Demo</a>
      </nav>
    </div>
  </header>

  <main class="min-h-[70vh] flex items-center">
    <div class="max-w-6xl mx-auto px-6 py-16 grid md:grid-cols-2 gap-12 items-center">
      <div>
        <h2 class="text-5xl md:text-7xl font-black leading-tight hero-gradient font-[space_grotesk] tracking-tight" data-hero-text>The Library Membership System Built For Students</h2>
        <p class="mt-6 text-lg text-white/80">Start your reading adventure here with Quixoria</p>

        <div class="mt-8 flex gap-4">
          <a href="{{ route('register') }}" class="btn-primary">Get Started — Free</a>
          <a href="{{ route('landing') }}#features" class="btn-secondary">View Features</a>
        </div>

        <div class="mt-8 grid grid-cols-3 gap-4 text-sm text-white/80">
          <div>
          <div class="font-black text-2xl hero-gradient font-[space_grotesk] data-stat">10k+</div>
            <div>Books tracked</div>
          </div>
          <div>
            <div class="font-black text-2xl hero-gradient font-[space_grotesk] data-stat">200+</div>
            <div>Institutions</div>
          </div>
          <div>
            <div class="font-black text-2xl hero-gradient font-[space_grotesk] data-stat">99.9%</div>
            <div>Uptime</div>
          </div>
        </div>
      </div>

      <div class="glass-deep rounded-2xl p-8 shadow-glass-glow">
        <div class="text-right text-sm text-white/70 mb-4">Trusted · Secure · Private</div>
        <div class="bg-slate-900 rounded-lg p-6">
          <h3 class="text-2xl font-semibold text-white mb-2">Premium</h3>
          <div class="text-4xl font-bold text-white">$349<span class="text-sm text-white/60">/month</span></div>
          <ul class="mt-4 text-white/80 space-y-2">
            <li>10 books per week</li>
            <li>21 days borrow period</li>
            <li>Reservations & priority support</li>
          </ul>
          <div class="mt-6">
            <a href="{{ route('register') }}" class="btn-primary w-full inline-block text-center">Subscribe to Premium</a>
          </div>
        </div>
      </div>
    </div>
  </main>

  <section id="features" class="py-20 bg-black/20">
    <div class="max-w-6xl mx-auto px-6">
      <h3 class="text-3xl text-white font-semibold mb-6">Features</h3>
      <div class="grid md:grid-cols-3 gap-6">
        <div class="feature-glow bg-surface p-8 rounded-xl hover:scale-[1.02] transition-transform">
          <h4 class="font-semibold text-white mb-2">Subscriptions</h4>
          <p class="text-white/80 text-sm">Create and manage tiered subscriptions with admin approval flows.</p>
        </div>
        <div class="feature-glow bg-surface p-8 rounded-xl hover:scale-[1.02] transition-transform">
          <h4 class="font-semibold text-white mb-2">Borrow Management</h4>
          <p class="text-white/80 text-sm">Weekly limits, overdue handling, and reservations in one place.</p>
        </div>
        <div class="feature-glow bg-surface p-8 rounded-xl hover:scale-[1.02] transition-transform">
          <h4 class="font-semibold text-white mb-2">Admin Tools</h4>
          <p class="text-white/80 text-sm">Pending inbox, bulk confirm/reject, force-activate and debug panels.</p>
        </div>
      </div>
    </div>
  </section>

  <footer class="py-8 px-6">
    <div class="max-w-6xl mx-auto text-sm text-white/70">© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</div>
  </footer>
</body>
</html>
