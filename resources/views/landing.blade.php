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
<body class="antialiased mesh-gradient relative overflow-x-hidden">
  <div class="particles"></div>
  <div class="pointer-events-none absolute inset-0 overflow-hidden">
    <div class="absolute -top-24 -left-24 w-72 h-72 rounded-full bg-cyan-400/10 blur-3xl motion-drift"></div>
    <div class="absolute top-40 -right-20 w-80 h-80 rounded-full bg-emerald-400/10 blur-3xl motion-drift motion-float-delayed"></div>
    <div class="absolute bottom-0 left-1/3 w-96 h-96 rounded-full bg-blue-500/10 blur-3xl motion-float"></div>
  </div>
  <div class="pointer-glow" aria-hidden="true"></div>
  <header class="w-full py-6 px-6 text-white relative z-10" style="background: linear-gradient(90deg, rgb(var(--surface-primary)) 0%, rgb(var(--bg-secondary)) 100%);">
    <div class="max-w-6xl mx-auto flex items-center justify-between">
      <div class="flex items-center gap-3">
        <img src="/images/logo.png" alt="Quixoria" class="w-16 h-16 md:w-20 md:h-20 rounded-xl bg-white/20 shadow-2xl object-contain border border-white/20 hover:scale-110 transition-all duration-300 motion-float">
        <div>
          <h1 class="text-xl font-black font-[space_grotesk] hero-gradient motion-rise">{{ config('app.name', 'Quixoria') }}</h1>
          <p class="text-sm text-white/80">Start your reading adventure here with Quixoria</p>
        </div>
      </div>
      <nav class="flex items-center gap-4">
        <a href="{{ route('login') }}" class="px-4 py-2 rounded-md text-white/90 hover:opacity-90">Sign In</a>
        <a href="{{ route('register') }}" class="btn-primary">Try Demo</a>
      </nav>
    </div>
  </header>

  <main class="min-h-[70vh] flex items-center relative z-10">
    <div class="max-w-6xl mx-auto px-6 py-16 grid md:grid-cols-2 gap-12 items-center">
      <div class="reveal-on-scroll">
        <div class="inline-flex items-center gap-2 rounded-full border border-cyan-400/20 bg-cyan-400/10 px-4 py-2 text-xs uppercase tracking-[0.25em] text-cyan-200 motion-shimmer">
          <span class="w-2 h-2 rounded-full bg-cyan-300 motion-float"></span>
          Campus library platform
        </div>

        <h2 class="mt-5 text-5xl md:text-7xl font-black leading-tight hero-gradient font-[space_grotesk] tracking-tight" data-hero-text>The Library Membership System Built For Students</h2>
        <p class="mt-6 text-lg text-white/80 motion-rise" style="animation-delay: 120ms;">Start your reading adventure here with Quixoria</p>

        <p class="mt-5 max-w-xl text-white/70 leading-7 motion-rise" style="animation-delay: 240ms;">
          Manage memberships, subscriptions, borrow requests, reservations, reminders, and reports from one polished dashboard.
          Designed for busy campuses that need clear workflows and fast approvals.
        </p>

        <div class="mt-8 flex gap-4 motion-rise" style="animation-delay: 360ms;">
          <a href="{{ route('register') }}" class="btn-primary">Get Started — Free</a>
          <a href="{{ route('landing') }}#features" class="btn-secondary">View Features</a>
        </div>

        <div class="mt-8 grid grid-cols-3 gap-4 text-sm text-white/80 reveal-on-scroll" data-reveal-delay="2">
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

      <div class="glass-deep rounded-2xl p-8 shadow-glass-glow reveal-on-scroll" data-reveal-delay="2">
        <div class="text-right text-sm text-white/70 mb-4">Trusted · Secure · Private</div>
        <div class="bg-slate-900 rounded-lg p-6 motion-shimmer">
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

  <section class="py-8 px-6 relative z-10">
    <div class="max-w-6xl mx-auto grid md:grid-cols-3 gap-4 text-white/80">
      <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur-md p-5 reveal-on-scroll" data-reveal-delay="1">
        <div class="text-sm uppercase tracking-[0.25em] text-cyan-300/80">For admins</div>
        <p class="mt-3 text-sm leading-6">Approve subscriptions, review audit logs, and keep membership records tidy without hopping between tools.</p>
      </div>
      <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur-md p-5 reveal-on-scroll" data-reveal-delay="2">
        <div class="text-sm uppercase tracking-[0.25em] text-cyan-300/80">For staff</div>
        <p class="mt-3 text-sm leading-6">Track active borrows, manage deadlines, send reminders, and handle return requests in one place.</p>
      </div>
      <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur-md p-5 reveal-on-scroll" data-reveal-delay="3">
        <div class="text-sm uppercase tracking-[0.25em] text-amber-300/80">For students</div>
        <p class="mt-3 text-sm leading-6">See your subscription status, request books, renew items, and stay on top of due dates.</p>
      </div>
    </div>
  </section>

  <section id="features" class="py-20 bg-black/20 relative z-10">
    <div class="max-w-6xl mx-auto px-6">
      <h3 class="text-3xl text-white font-semibold mb-6 reveal-on-scroll">Features</h3>
      <div class="grid md:grid-cols-3 gap-6">
        <div class="feature-glow bg-surface p-8 rounded-xl hover:scale-[1.02] transition-transform reveal-on-scroll" data-reveal-delay="1">
          <h4 class="font-semibold text-white mb-2">Subscriptions</h4>
          <p class="text-white/80 text-sm">Create and manage tiered subscriptions with admin approval flows.</p>
        </div>
        <div class="feature-glow bg-surface p-8 rounded-xl hover:scale-[1.02] transition-transform reveal-on-scroll" data-reveal-delay="2">
          <h4 class="font-semibold text-white mb-2">Borrow Management</h4>
          <p class="text-white/80 text-sm">Weekly limits, overdue handling, and reservations in one place.</p>
        </div>
        <div class="feature-glow bg-surface p-8 rounded-xl hover:scale-[1.02] transition-transform reveal-on-scroll" data-reveal-delay="3">
          <h4 class="font-semibold text-white mb-2">Admin Tools</h4>
          <p class="text-white/80 text-sm">Pending inbox, bulk confirm/reject, force-activate and debug panels.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="py-20 relative z-10">
    <div class="max-w-6xl mx-auto px-6 grid lg:grid-cols-2 gap-10 items-start">
      <div class="reveal-on-scroll">
        <h3 class="text-3xl md:text-4xl font-black text-white font-[space_grotesk]">A simple workflow that keeps the library moving</h3>
        <p class="mt-4 text-white/70 leading-7 max-w-2xl">
          The platform is built around a straightforward loop: enroll members, approve plans, check out books, and keep everyone informed before due dates slip past.
        </p>

        <div class="mt-8 space-y-4">
          <div class="rounded-2xl border border-white/10 bg-white/5 p-5 reveal-on-scroll" data-reveal-delay="1">
            <div class="text-cyan-300 font-semibold">1. Onboard members</div>
            <p class="mt-2 text-sm text-white/75">Create student, staff, or admin accounts and assign the right access level from day one.</p>
          </div>
          <div class="rounded-2xl border border-white/10 bg-white/5 p-5 reveal-on-scroll" data-reveal-delay="2">
            <div class="text-cyan-300 font-semibold">2. Manage borrowing</div>
            <p class="mt-2 text-sm text-white/75">Handle active borrows, renewals, reservations, and return requests with less manual work.</p>
          </div>
          <div class="rounded-2xl border border-white/10 bg-white/5 p-5 reveal-on-scroll" data-reveal-delay="3">
            <div class="text-amber-300 font-semibold">3. Stay ahead of deadlines</div>
            <p class="mt-2 text-sm text-white/75">Use reminders, overdue views, and reports to intervene before issues become backlogs.</p>
          </div>
        </div>
      </div>

      <div class="grid gap-4 sm:grid-cols-2 reveal-on-scroll" data-reveal-delay="2">
        <div class="rounded-3xl p-6 bg-gradient-to-br from-cyan-500/15 to-slate-900 border border-cyan-400/20 shadow-2xl motion-float">
          <div class="text-sm text-cyan-200 uppercase tracking-[0.3em]">Reporting</div>
          <p class="mt-3 text-white text-lg font-semibold">See what is happening now</p>
          <p class="mt-2 text-white/70 text-sm leading-6">Borrow trends, overdue activity, revenue, and student behavior at a glance.</p>
        </div>
        <div class="rounded-3xl p-6 bg-gradient-to-br from-cyan-500/15 to-slate-900 border border-cyan-400/20 shadow-2xl motion-float-delayed">
          <div class="text-sm text-cyan-200 uppercase tracking-[0.3em]">Security</div>
          <p class="mt-3 text-white text-lg font-semibold">Keep access under control</p>
          <p class="mt-2 text-white/70 text-sm leading-6">Role-based navigation, restricted accounts, and audit-friendly actions help keep things tidy.</p>
        </div>
        <div class="sm:col-span-2 rounded-3xl p-6 bg-white/5 border border-white/10 backdrop-blur-md">
          <div class="text-sm text-white/60 uppercase tracking-[0.3em]">What you can manage</div>
          <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm text-white/80">
            <span class="rounded-xl bg-white/5 px-3 py-3 text-center border border-white/10">Members</span>
            <span class="rounded-xl bg-white/5 px-3 py-3 text-center border border-white/10">Subscriptions</span>
            <span class="rounded-xl bg-white/5 px-3 py-3 text-center border border-white/10">Borrow Requests</span>
            <span class="rounded-xl bg-white/5 px-3 py-3 text-center border border-white/10">Reservations</span>
            <span class="rounded-xl bg-white/5 px-3 py-3 text-center border border-white/10">Payments</span>
            <span class="rounded-xl bg-white/5 px-3 py-3 text-center border border-white/10">Notifications</span>
            <span class="rounded-xl bg-white/5 px-3 py-3 text-center border border-white/10">Reports</span>
            <span class="rounded-xl bg-white/5 px-3 py-3 text-center border border-white/10">Audit Logs</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="py-20 bg-black/20 relative z-10">
    <div class="max-w-6xl mx-auto px-6">
      <div class="flex items-end justify-between gap-4 flex-wrap mb-8">
        <div>
          <h3 class="text-3xl text-white font-semibold reveal-on-scroll">Why teams choose it</h3>
          <p class="mt-3 text-white/70 max-w-2xl">Less manual tracking, clearer handoffs, and a cleaner experience for every role.</p>
        </div>
        <a href="{{ route('register') }}" class="btn-secondary">Start now</a>
      </div>

      <div class="grid md:grid-cols-2 xl:grid-cols-4 gap-6">
        <div class="rounded-2xl border border-white/10 bg-white/5 p-6 reveal-on-scroll" data-reveal-delay="1">
          <div class="text-cyan-300 font-semibold">Fast approvals</div>
          <p class="mt-3 text-sm text-white/75 leading-6">Handle subscription and request workflows with fewer clicks and less confusion.</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/5 p-6 reveal-on-scroll" data-reveal-delay="2">
          <div class="text-amber-300 font-semibold">Role-aware UI</div>
          <p class="mt-3 text-sm text-white/75 leading-6">Admins, staff, and students each get only the tools they need.</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/5 p-6 reveal-on-scroll" data-reveal-delay="3">
          <div class="text-amber-300 font-semibold">Deadline visibility</div>
          <p class="mt-3 text-sm text-white/75 leading-6">Reduce overdue items with reminders, status badges, and a dedicated deadline dashboard.</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/5 p-6 reveal-on-scroll" data-reveal-delay="4">
          <div class="text-emerald-300 font-semibold">Cleaner records</div>
          <p class="mt-3 text-sm text-white/75 leading-6">Audit logs and structured data make it easier to review what happened and when.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="py-20 relative z-10">
    <div class="max-w-6xl mx-auto px-6 grid lg:grid-cols-3 gap-6">
      <div class="lg:col-span-1 reveal-on-scroll">
        <h3 class="text-3xl text-white font-semibold">Built for practical library work</h3>
        <p class="mt-4 text-white/70 leading-7">
          The interface is intentionally simple where it needs to be, and detailed where staff need control. That keeps training time down and makes day-to-day operations easier.
        </p>
      </div>
      <div class="lg:col-span-2 grid md:grid-cols-3 gap-4">
        <div class="rounded-2xl border border-white/10 bg-white/5 p-5 reveal-on-scroll" data-reveal-delay="1">
          <p class="text-sm text-white/60">Borrow flow</p>
          <p class="mt-2 text-white font-semibold">Request, approve, track, return</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/5 p-5 reveal-on-scroll" data-reveal-delay="2">
          <p class="text-sm text-white/60">Membership flow</p>
          <p class="mt-2 text-white font-semibold">Plans, renewals, upgrades, restrictions</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/5 p-5 reveal-on-scroll" data-reveal-delay="3">
          <p class="text-sm text-white/60">Operations flow</p>
          <p class="mt-2 text-white font-semibold">Notifications, reports, audit trails</p>
        </div>
      </div>
    </div>
  </section>

  <section class="py-20 bg-gradient-to-r from-cyan-500/10 via-slate-900/10 to-sky-500/10 border-y border-white/10 relative z-10">
    <div class="max-w-5xl mx-auto px-6 text-center">
      <h3 class="text-3xl md:text-5xl font-black text-white font-[space_grotesk] reveal-on-scroll">Ready to make your library feel organized again?</h3>
      <p class="mt-4 text-white/75 max-w-2xl mx-auto leading-7 reveal-on-scroll" data-reveal-delay="1">
        Get started with a system that keeps subscriptions, borrowing, and staff workflows in one place.
      </p>
      <div class="mt-8 flex flex-col sm:flex-row justify-center gap-4 reveal-on-scroll" data-reveal-delay="2">
        <a href="{{ route('register') }}" class="btn-primary">Create an account</a>
        <a href="{{ route('login') }}" class="btn-secondary">Sign in</a>
      </div>
    </div>
  </section>

  <section class="py-20 relative z-10">
    <div class="max-w-6xl mx-auto px-6 grid md:grid-cols-2 gap-6">
      <div class="rounded-3xl border border-white/10 bg-white/5 p-8 reveal-on-scroll" data-reveal-delay="1">
        <div class="text-sm uppercase tracking-[0.3em] text-cyan-300">Testimonial</div>
        <p class="mt-4 text-xl text-white leading-8">
          "We finally have a single place to manage users, subscriptions, and overdue handling without juggling spreadsheets."
        </p>
        <p class="mt-6 text-white/60 text-sm">Library operations team</p>
      </div>
      <div class="rounded-3xl border border-white/10 bg-white/5 p-8 reveal-on-scroll" data-reveal-delay="2">
        <div class="text-sm uppercase tracking-[0.3em] text-cyan-300">FAQ</div>
        <div class="mt-4 space-y-4 text-white/80">
          <div>
            <div class="font-semibold text-white">Who is this for?</div>
            <p class="mt-2 text-sm leading-6">It is built for admins, library staff, and students in a membership-based library workflow.</p>
          </div>
          <div>
            <div class="font-semibold text-white">Does it handle overdue reminders?</div>
            <p class="mt-2 text-sm leading-6">Yes. Staff can send reminders and review deadlines from the dashboard.</p>
          </div>
          <div>
            <div class="font-semibold text-white">Can I control who sees what?</div>
            <p class="mt-2 text-sm leading-6">Yes. The app uses role-based access and restricted account handling.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <footer class="py-8 px-6">
    <div class="max-w-6xl mx-auto text-sm text-white/70">© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</div>
  </footer>
</body>
</html>
