<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BloodLink — Save Lives, Donate Blood</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.bunny.net" />
  <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />
  <style>
    * { font-family: 'Inter', sans-serif; }

    /* ── Animated gradient background ── */
    .hero-bg {
      background: linear-gradient(135deg, #0f0f0f 0%, #1a0000 40%, #2d0000 70%, #0f0f0f 100%);
      background-size: 400% 400%;
      animation: gradShift 10s ease infinite;
    }
    @keyframes gradShift {
      0%   { background-position: 0% 50%; }
      50%  { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    /* ── Typewriter ── */
    .typewriter {
      overflow: hidden;
      border-right: 3px solid #ef4444;
      white-space: nowrap;
      animation: typing 3.5s steps(30,end) forwards, blink 0.75s step-end infinite;
      max-width: fit-content;
    }
    @keyframes typing  { from { width: 0 } to { width: 100% } }
    @keyframes blink   { 0%,100% { border-color: transparent } 50% { border-color: #ef4444 } }

    /* ── Pulse ring on blood drop ── */
    .pulse-ring {
      animation: pulseRing 2s ease-out infinite;
    }
    @keyframes pulseRing {
      0%   { transform: scale(1);   opacity: 0.6; }
      70%  { transform: scale(1.8); opacity: 0;   }
      100% { transform: scale(1.8); opacity: 0;   }
    }

    /* ── Floating cards ── */
    .float-card { animation: floatUp 6s ease-in-out infinite; }
    .float-card:nth-child(2) { animation-delay: 1.5s; }
    .float-card:nth-child(3) { animation-delay: 3s; }
    @keyframes floatUp {
      0%,100% { transform: translateY(0px); }
      50%      { transform: translateY(-12px); }
    }

    /* ── Count-up numbers ── */
    .stat-num { transition: all 0.5s ease; }

    /* ── Scroll reveal ── */
    .reveal {
      opacity: 0;
      transform: translateY(40px);
      transition: opacity 0.7s ease, transform 0.7s ease;
    }
    .reveal.visible {
      opacity: 1;
      transform: translateY(0);
    }

    /* ── Step connector line ── */
    .step-line::before {
      content: '';
      position: absolute;
      top: 24px;
      left: calc(50% + 56px);
      width: calc(100% - 112px);
      height: 2px;
      background: linear-gradient(90deg, #ef4444, #7f1d1d);
    }

    /* ── Blood drop SVG bounce ── */
    .drop-bounce { animation: dropBounce 3s ease-in-out infinite; }
    @keyframes dropBounce {
      0%,100% { transform: translateY(0) scale(1); }
      50%      { transform: translateY(-16px) scale(1.05); }
    }

    /* ── Particle dots ── */
    .particle {
      position: absolute;
      border-radius: 50%;
      background: rgba(239,68,68,0.2);
      animation: particleDrift linear infinite;
    }
    @keyframes particleDrift {
      0%   { transform: translateY(0)   rotate(0deg);   opacity: 0.4; }
      100% { transform: translateY(-120vh) rotate(360deg); opacity: 0; }
    }

    /* ── Navbar scroll effect ── */
    .navbar-scrolled {
      background: rgba(10,10,10,0.95) !important;
      backdrop-filter: blur(12px);
      box-shadow: 0 2px 20px rgba(239,68,68,0.15);
    }

    /* ── Hover card glow ── */
    .glow-card:hover {
      box-shadow: 0 0 30px rgba(239,68,68,0.25);
      transform: translateY(-4px);
      transition: all 0.3s ease;
    }
    .glow-card { transition: all 0.3s ease; }

    /* ── Role cards ── */
    .role-card {
      background: linear-gradient(135deg, rgba(255,255,255,0.03) 0%, rgba(255,255,255,0.06) 100%);
      border: 1px solid rgba(255,255,255,0.08);
      transition: all 0.35s ease;
      cursor: pointer;
    }
    .role-card:hover {
      border-color: rgba(239,68,68,0.5);
      background: linear-gradient(135deg, rgba(239,68,68,0.05) 0%, rgba(239,68,68,0.1) 100%);
      transform: translateY(-6px) scale(1.02);
      box-shadow: 0 20px 40px rgba(239,68,68,0.15);
    }

    /* ── FAQ accordion ── */
    .faq-answer { max-height: 0; overflow: hidden; transition: max-height 0.4s ease; }
    .faq-answer.open { max-height: 200px; }
    .faq-icon { transition: transform 0.3s ease; }
    .faq-icon.rotated { transform: rotate(45deg); }

    /* ── Scrollbar ── */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: #0f0f0f; }
    ::-webkit-scrollbar-thumb { background: #ef4444; border-radius: 3px; }
  </style>
</head>

<body class="bg-black text-white overflow-x-hidden">

<nav id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 py-4 px-6">
  <div class="max-w-7xl mx-auto flex items-center justify-between">

    <!-- Logo -->
    <a href="/" class="flex items-center gap-3 group">
      <div class="relative">
        <div class="w-10 h-10 bg-red-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
          <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2C12 2 4 10.5 4 15a8 8 0 0016 0C20 10.5 12 2 12 2z"/>
          </svg>
        </div>
        <div class="absolute inset-0 w-10 h-10 bg-red-600 rounded-xl pulse-ring"></div>
      </div>
      <span class="text-xl font-bold tracking-tight">Blood<span class="text-red-500">Link</span></span>
    </a>

    <!-- Nav links (desktop) -->
    <div class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-300">
      <a href="#how-it-works" class="hover:text-red-400 transition-colors">How It Works</a>
      <a href="#blood-groups" class="hover:text-red-400 transition-colors">Blood Groups</a>
      <a href="#stats"        class="hover:text-red-400 transition-colors">Impact</a>
      <a href="#faq"          class="hover:text-red-400 transition-colors">FAQ</a>
    </div>

    <!-- Auth buttons -->
    <div class="flex items-center gap-3">
      <!-- Login dropdown -->
      <div class="relative group">
        <button class="px-4 py-2 text-sm font-medium text-gray-300 hover:text-white border border-gray-700 hover:border-red-500 rounded-lg transition-all duration-200">
          Sign In ▾
        </button>
        <div class="absolute right-0 top-full mt-2 w-44 bg-gray-900 border border-gray-800 rounded-xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 overflow-hidden">
          <a href="{{ url('/donor/login') }}"   class="flex items-center gap-3 px-4 py-3 text-sm text-gray-300 hover:bg-red-950 hover:text-white transition-colors">
            <span class="text-red-400">💉</span> Donor
          </a>
          <a href="{{ url('/hospital/login') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-300 hover:bg-red-950 hover:text-white transition-colors border-t border-gray-800">
            <span class="text-red-400">🏥</span> Hospital
          </a>
          <a href="{{ url('/patient/login') }}"  class="flex items-center gap-3 px-4 py-3 text-sm text-gray-300 hover:bg-red-950 hover:text-white transition-colors border-t border-gray-800">
            <span class="text-red-400">🩺</span> Patient
          </a>
        </div>
      </div>

      <!-- Register dropdown -->
      <div class="relative group">
        <button class="px-4 py-2 text-sm font-semibold bg-red-600 hover:bg-red-500 text-white rounded-lg transition-all duration-200 shadow-lg shadow-red-900/40">
          Register ▾
        </button>
        <div class="absolute right-0 top-full mt-2 w-44 bg-gray-900 border border-gray-800 rounded-xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 overflow-hidden">
          <a href="{{ url('/donor/register') }}"   class="flex items-center gap-3 px-4 py-3 text-sm text-gray-300 hover:bg-red-950 hover:text-white transition-colors">
            <span class="text-red-400">💉</span> As Donor
          </a>
          <a href="{{ url('/hospital/register') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-300 hover:bg-red-950 hover:text-white transition-colors border-t border-gray-800">
            <span class="text-red-400">🏥</span> As Hospital
          </a>
          <a href="{{ url('/patient/register') }}"  class="flex items-center gap-3 px-4 py-3 text-sm text-gray-300 hover:bg-red-950 hover:text-white transition-colors border-t border-gray-800">
            <span class="text-red-400">🩺</span> As Patient
          </a>
        </div>
      </div>
    </div>

  </div>
</nav>

<section class="hero-bg relative min-h-screen flex items-center justify-center overflow-hidden pt-20">

  <!-- Floating particles -->
  <div id="particles" class="absolute inset-0 pointer-events-none"></div>

  <!-- Decorative grid overlay -->
  <div class="absolute inset-0 opacity-5"
       style="background-image: linear-gradient(rgba(239,68,68,0.3) 1px, transparent 1px),
                                 linear-gradient(90deg, rgba(239,68,68,0.3) 1px, transparent 1px);
              background-size: 60px 60px;">
  </div>

  <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-16 items-center relative z-10">

    <!-- Left: Text -->
    <div>
      <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-red-950/60 border border-red-800/50 text-red-400 text-sm font-medium mb-8">
        <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
        Every drop counts
      </div>

      <h1 class="text-5xl lg:text-7xl font-extrabold leading-tight mb-4">
        One Donation.<br/>
        <span class="text-red-500">Three Lives</span> Saved.
      </h1>

      <!-- Typewriter sub-heading -->
      <div class="h-10 mb-6">
        <p id="typewriter-target" class="text-xl text-gray-400 font-medium typewriter"></p>
      </div>

      <p class="text-gray-400 text-lg leading-relaxed mb-10 max-w-lg">
        BloodLink connects donors, hospitals, and patients in a seamless platform — making emergency blood requests faster, smarter, and life-saving.
      </p>

      <div class="flex flex-wrap gap-4">
        <a href="{{ url('/donor/register') }}"
           class="group flex items-center gap-3 px-8 py-4 bg-red-600 hover:bg-red-500 text-white font-semibold rounded-xl text-base shadow-xl shadow-red-900/40 transition-all duration-300 hover:scale-105">
          <svg class="w-5 h-5 group-hover:scale-110 transition-transform" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2C12 2 4 10.5 4 15a8 8 0 0016 0C20 10.5 12 2 12 2z"/>
          </svg>
          Become a Donor
        </a>
        <a href="#how-it-works"
           class="flex items-center gap-3 px-8 py-4 border border-gray-700 hover:border-red-500 text-gray-300 hover:text-white font-semibold rounded-xl text-base transition-all duration-300 hover:scale-105">
          How It Works
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </a>
      </div>
      <br><br>
    </div>

    <!-- Right: Animated blood drop illustration -->
    <div class="hidden lg:flex justify-center items-center">
      <div class="relative w-80 h-80">
        <!-- Outer glow rings -->
        <div class="absolute inset-0 rounded-full bg-red-600/5 border border-red-900/30 animate-ping" style="animation-duration:3s"></div>
        <div class="absolute inset-8 rounded-full bg-red-600/8 border border-red-800/20 animate-ping" style="animation-duration:3s;animation-delay:0.5s"></div>

        <!-- Main blood drop -->
        <div class="absolute inset-16 flex items-center justify-center drop-bounce">
          <svg viewBox="0 0 100 120" class="w-48 h-48 drop-shadow-2xl filter" style="filter:drop-shadow(0 0 30px rgba(239,68,68,0.6))">
            <defs>
              <linearGradient id="dropGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" style="stop-color:#ef4444"/>
                <stop offset="100%" style="stop-color:#7f1d1d"/>
              </linearGradient>
            </defs>
            <path d="M50 5 C50 5 10 55 10 75 C10 97 28 110 50 110 C72 110 90 97 90 75 C90 55 50 5 50 5Z"
                  fill="url(#dropGrad)"/>
            <!-- shine -->
            <ellipse cx="38" cy="58" rx="8" ry="14" fill="rgba(255,255,255,0.15)" transform="rotate(-20 38 58)"/>
          </svg>
        </div>
      </div>
    </div>

  </div>
</section>

<section class="py-24 bg-gray-950">
  <div class="max-w-7xl mx-auto px-6">

    <div class="text-center mb-16 reveal">
      <span class="text-red-500 text-sm font-semibold uppercase tracking-widest">Who Is This For</span>
      <h2 class="text-4xl font-bold mt-3 mb-4">One Platform. Three Roles.</h2>
      <p class="text-gray-400 max-w-xl mx-auto">Whether you want to give, receive, or coordinate — BloodLink has a place for you.</p>
    </div>

    <div class="grid md:grid-cols-3 gap-8">

      <!-- Donor -->
      <div class="role-card rounded-2xl p-8 reveal">
        <div class="w-16 h-16 bg-red-950/60 border border-red-900/50 rounded-2xl flex items-center justify-center mb-6 text-3xl">
          💉
        </div>
        <h3 class="text-xl font-bold mb-3 text-white">Donor</h3>
        <p class="text-gray-400 text-sm leading-relaxed mb-6">
          Register once, save lives repeatedly. Manage your availability, choose your preferred hospital, and track your donation history.
        </p>
        <ul class="space-y-2 text-sm text-gray-500 mb-8">
          <li class="flex items-center gap-2"><span class="text-red-500">✓</span> Select your hospital at signup</li>
          <li class="flex items-center gap-2"><span class="text-red-500">✓</span> Toggle availability anytime</li>
          <li class="flex items-center gap-2"><span class="text-red-500">✓</span> View full donation history</li>
        </ul>
        <a href="{{ url('/donor/register') }}"
           class="block text-center py-3 px-6 bg-red-600/10 hover:bg-red-600 border border-red-800 hover:border-red-500 text-red-400 hover:text-white rounded-xl text-sm font-semibold transition-all duration-300">
          Join as Donor
        </a>
      </div>

      <!-- Hospital -->
      <div class="role-card rounded-2xl p-8 reveal" style="transition-delay:0.15s">
        <div class="w-16 h-16 bg-blue-950/60 border border-blue-900/50 rounded-2xl flex items-center justify-center mb-6 text-3xl">
          🏥
        </div>
        <h3 class="text-xl font-bold mb-3 text-white">Hospital</h3>
        <p class="text-gray-400 text-sm leading-relaxed mb-6">
          View all blood requests in real time. Match donors to patients, verify eligibility, and record donations — all from one dashboard.
        </p>
        <ul class="space-y-2 text-sm text-gray-500 mb-8">
          <li class="flex items-center gap-2"><span class="text-blue-400">✓</span> See all open blood requests</li>
          <li class="flex items-center gap-2"><span class="text-blue-400">✓</span> Verify donor eligibility</li>
          <li class="flex items-center gap-2"><span class="text-blue-400">✓</span> Download & export necessary reports</li>
        </ul>
        <a href="{{ url('/hospital/register') }}"
           class="block text-center py-3 px-6 bg-blue-600/10 hover:bg-blue-600 border border-blue-800 hover:border-blue-500 text-blue-400 hover:text-white rounded-xl text-sm font-semibold transition-all duration-300">
          Register Hospital
        </a>
      </div>

      <!-- Patient -->
      <div class="role-card rounded-2xl p-8 reveal" style="transition-delay:0.3s">
        <div class="w-16 h-16 bg-purple-950/60 border border-purple-900/50 rounded-2xl flex items-center justify-center mb-6 text-3xl">
          🩺
        </div>
        <h3 class="text-xl font-bold mb-3 text-white">Patient</h3>
        <p class="text-gray-400 text-sm leading-relaxed mb-6">
          Submit a blood request in under a minute. Your request reaches every registered hospital instantly, including emergency alerts.
        </p>
        <ul class="space-y-2 text-sm text-gray-500 mb-8">
          <li class="flex items-center gap-2"><span class="text-purple-400">✓</span> Request specific blood group</li>
          <li class="flex items-center gap-2"><span class="text-purple-400">✓</span> Mark as emergency</li>
          <li class="flex items-center gap-2"><span class="text-purple-400">✓</span> Track request status</li>
        </ul>
        <a href="{{ url('/patient/register') }}"
           class="block text-center py-3 px-6 bg-purple-600/10 hover:bg-purple-600 border border-purple-800 hover:border-purple-500 text-purple-400 hover:text-white rounded-xl text-sm font-semibold transition-all duration-300">
          I Need Blood
        </a>
      </div>

    </div>
  </div>
</section>

<section id="how-it-works" class="py-24 bg-black">
  <div class="max-w-7xl mx-auto px-6">

    <div class="text-center mb-20 reveal">
      <span class="text-red-500 text-sm font-semibold uppercase tracking-widest">The Process</span>
      <h2 class="text-4xl font-bold mt-3 mb-4">How BloodLink Works</h2>
      <p class="text-gray-400 max-w-xl mx-auto">From request to donation in four simple steps.</p>
    </div>

    <div class="grid md:grid-cols-4 gap-8 relative">

      <!-- Step connector lines (desktop only) -->
      <div class="hidden md:block absolute top-12 left-1/4 right-1/4 h-0.5 bg-gradient-to-r from-red-900 via-red-600 to-red-900 z-0" style="left:calc(12.5% + 40px);right:calc(12.5% + 40px)"></div>

      <!-- Step 1 -->
      <div class="reveal text-center relative z-10">
        <div class="w-20 h-20 mx-auto mb-6 bg-gray-900 border-2 border-red-600 rounded-2xl flex items-center justify-center text-3xl glow-card">
          📝
        </div>
        <div class="text-xs text-red-500 font-bold uppercase tracking-widest mb-2">Step 01</div>
        <h3 class="font-bold text-lg mb-2">Register</h3>
        <p class="text-gray-500 text-sm leading-relaxed">Sign up as a donor, hospital, or patient. Fill your profile with the required information.</p>
      </div>

      <!-- Step 2 -->
      <div class="reveal text-center relative z-10" style="transition-delay:0.15s">
        <div class="w-20 h-20 mx-auto mb-6 bg-gray-900 border-2 border-red-600 rounded-2xl flex items-center justify-center text-3xl glow-card">
          🩸
        </div>
        <div class="text-xs text-red-500 font-bold uppercase tracking-widest mb-2">Step 02</div>
        <h3 class="font-bold text-lg mb-2">Request</h3>
        <p class="text-gray-500 text-sm leading-relaxed">Patients submit blood requests specifying the required blood group and urgency level.</p>
      </div>

      <!-- Step 3 -->
      <div class="reveal text-center relative z-10" style="transition-delay:0.3s">
        <div class="w-20 h-20 mx-auto mb-6 bg-gray-900 border-2 border-red-600 rounded-2xl flex items-center justify-center text-3xl glow-card">
          🔍
        </div>
        <div class="text-xs text-red-500 font-bold uppercase tracking-widest mb-2">Step 03</div>
        <h3 class="font-bold text-lg mb-2">Match</h3>
        <p class="text-gray-500 text-sm leading-relaxed">Hospitals review requests, verify donor eligibility — age, blood group, and 90-day rule.</p>
      </div>

      <!-- Step 4 -->
      <div class="reveal text-center relative z-10" style="transition-delay:0.45s">
        <div class="w-20 h-20 mx-auto mb-6 bg-gray-900 border-2 border-red-600 rounded-2xl flex items-center justify-center text-3xl glow-card">
          ❤️
        </div>
        <div class="text-xs text-red-500 font-bold uppercase tracking-widest mb-2">Step 04</div>
        <h3 class="font-bold text-lg mb-2">Donate</h3>
        <p class="text-gray-500 text-sm leading-relaxed">Donation is recorded, donor history updated, and the patient request marked fulfilled.</p>
      </div>

    </div>
  </div>
</section>

<section id="blood-groups" class="py-24 bg-gray-950">
  <div class="max-w-5xl mx-auto px-6">

    <div class="text-center mb-16 reveal">
      <span class="text-red-500 text-sm font-semibold uppercase tracking-widest">Quick Reference</span>
      <h2 class="text-4xl font-bold mt-3 mb-4">Blood Group Compatibility</h2>
      <p class="text-gray-400 max-w-xl mx-auto">Know which blood group can donate to whom before you submit a request.</p>
    </div>

    <div class="reveal overflow-hidden rounded-2xl border border-gray-800">
      <table class="w-full text-sm">
        <thead>
          <tr class="bg-red-950/60">
            <th class="px-6 py-4 text-left text-red-400 font-semibold">Blood Group</th>
            <th class="px-6 py-4 text-left text-red-400 font-semibold">Can Donate To</th>
            <th class="px-6 py-4 text-left text-red-400 font-semibold">Can Receive From</th>
            <th class="px-6 py-4 text-left text-red-400 font-semibold">Type</th>
          </tr>
        </thead>
        <tbody>
          @php
          $groups = [
            ['A+',  'A+, AB+',               'A+, A-, O+, O-',           'Common'],
            ['A-',  'A+, A-, AB+, AB-',       'A-, O-',                   'Rare'],
            ['B+',  'B+, AB+',               'B+, B-, O+, O-',           'Common'],
            ['B-',  'B+, B-, AB+, AB-',       'B-, O-',                   'Rare'],
            ['AB+', 'AB+',                   'All blood groups',          'Universal Recipient'],
            ['AB-', 'AB+, AB-',              'A-, B-, AB-, O-',          'Rare'],
            ['O+',  'A+, B+, O+, AB+',       'O+, O-',                   'Most Common'],
            ['O-',  'All blood groups',       'O-',                       'Universal Donor'],
          ];
          @endphp
          @foreach($groups as $i => $g)
          <tr class="{{ $i % 2 === 0 ? 'bg-gray-900/40' : 'bg-transparent' }} hover:bg-red-950/20 transition-colors border-t border-gray-800/50">
            <td class="px-6 py-4 font-bold text-white text-base">{{ $g[0] }}</td>
            <td class="px-6 py-4 text-gray-400">{{ $g[1] }}</td>
            <td class="px-6 py-4 text-gray-400">{{ $g[2] }}</td>
            <td class="px-6 py-4">
              <span class="px-3 py-1 rounded-full text-xs font-medium
                {{ str_contains($g[3], 'Universal') ? 'bg-green-950/60 text-green-400 border border-green-900' : '' }}
                {{ $g[3] === 'Common' ? 'bg-blue-950/60 text-blue-400 border border-blue-900' : '' }}
                {{ $g[3] === 'Rare' ? 'bg-yellow-950/60 text-yellow-400 border border-yellow-900' : '' }}
                {{ $g[3] === 'Most Common' ? 'bg-red-950/60 text-red-400 border border-red-900' : '' }}
              ">{{ $g[3] }}</span>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

  </div>
</section>

<section id="stats" class="py-24 bg-black relative overflow-hidden">

  <!-- Background decoration -->
  <div class="absolute inset-0 opacity-20"
       style="background: radial-gradient(ellipse at 50% 50%, rgba(239,68,68,0.15) 0%, transparent 70%)">
  </div>

  <div class="max-w-5xl mx-auto px-6 relative z-10">

    <div class="text-center mb-16 reveal">
      <span class="text-red-500 text-sm font-semibold uppercase tracking-widest">Our Impact</span>
      <h2 class="text-4xl font-bold mt-3 mb-4">Numbers That Matter</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      @php
      $stats = [
        ['50', 'Registered Donors',   '50', '💉'],
        ['50',   'Partner Hospitals',   '50',  '🏥'],
        ['50','Lives Impacted',      '50','❤️'],
      ];
      @endphp
      @foreach($stats as $i => $s)
      <div class="reveal text-center glow-card bg-gray-900/50 border border-gray-800 rounded-2xl p-8" style="transition-delay:{{ $i * 0.1 }}s">
        <div class="text-4xl mb-4">{{ $s[3] }}</div>
        <div class="text-3xl font-extrabold text-red-400 mb-2" data-count="{{ $s[2] }}">{{ $s[0] }}</div>
        <div class="text-gray-500 text-sm">{{ $s[1] }}</div>
      </div>
      @endforeach
    </div>

  </div>
</section>

<section class="py-24 bg-gray-950">
  <div class="max-w-7xl mx-auto px-6">

    <div class="text-center mb-16 reveal">
      <span class="text-red-500 text-sm font-semibold uppercase tracking-widest">Why Choose Us</span>
      <h2 class="text-4xl font-bold mt-3 mb-4">Built for Emergencies.<br/>Designed for Simplicity.</h2>
      <p class="text-gray-400 max-w-2xl mx-auto">Every feature is crafted to make blood donation faster, safer, and more accessible for everyone involved.</p>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
      @php
      $features = [
        ['⚡', 'Real-time Requests', 'Patient requests are visible to all hospitals instantly, reducing response time in emergencies.'],
        ['🔒', 'Secure & Verified', 'All donors are eligibility-checked — age, blood group compatibility, and 90-day donation gap enforced.'],
        ['📊', 'Smart Matching', 'Hospitals can filter and sort donors by blood group, district, and availability with a single click.'],
        ['📧', 'Instant Notifications', 'Hospitals can notify both donors and patients via email once a match is confirmed.'],
        ['📥', 'Export & Reports', 'Hospitals can download donor lists, patient lists, and full donation records anytime.'],
        ['🌍', 'Nationwide Coverage', 'Connect with donors and hospitals across all 64 districts of Bangladesh.'],
      ];
      @endphp
      @foreach($features as $f)
      <div class="flex items-start gap-5 p-6 bg-gray-900/50 border border-gray-800 rounded-2xl hover:border-red-900/50 transition-all duration-300 group reveal">
        <div class="w-12 h-12 flex-shrink-0 bg-gray-800 border border-gray-700 group-hover:border-red-700 rounded-xl flex items-center justify-center text-xl transition-all duration-300">
          {{ $f[0] }}
        </div>
        <div>
          <h4 class="font-semibold text-white mb-2 group-hover:text-red-400 transition-colors">{{ $f[1] }}</h4>
          <p class="text-gray-500 text-sm leading-relaxed">{{ $f[2] }}</p>
        </div>
      </div>
      @endforeach
    </div>

  </div>
</section>

<section id="faq" class="py-24 bg-black">
  <div class="max-w-3xl mx-auto px-6">

    <div class="text-center mb-16 reveal">
      <span class="text-red-500 text-sm font-semibold uppercase tracking-widest">Questions</span>
      <h2 class="text-4xl font-bold mt-3 mb-4">Frequently Asked</h2>
    </div>

    <div class="space-y-4 reveal">
      @php
      $faqs = [
        ['Who can donate blood?',
         'Anyone aged 18 or above and who has not donated blood in the last 90 days is eligible to donate.'],
        ['How does the hospital verify eligibility?',
         'When a hospital processes a donation, the system automatically checks the donor\'s age (≥18), last donation date (>90 days), and blood group compatibility with the patient request.'],
        ['Can I submit a request without creating an account?',
         'No. Patient registration is required to post a blood request. This ensures hospitals can reach you and track the request status.'],
        ['Can a donor choose which hospital to donate at?',
         'Yes. Donors select their preferred hospital during signup. They are associated with that hospital but the hospital handles the actual matching process.'],
        ['How long does it take to get a donor match?',
         'Since requests are visible to all registered hospitals immediately, a match can happen within minutes in areas with active donors and hospitals.'],
      ];
      @endphp
      @foreach($faqs as $i => $faq)
      <div class="bg-gray-900 border border-gray-800 hover:border-gray-700 rounded-xl overflow-hidden transition-colors faq-item">
        <button onclick="toggleFaq(this)"
                class="w-full flex items-center justify-between px-6 py-5 text-left text-white font-medium hover:text-red-400 transition-colors">
          <span>{{ $faq[0] }}</span>
          <svg class="w-5 h-5 text-gray-500 faq-icon flex-shrink-0 ml-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
          </svg>
        </button>
        <div class="faq-answer">
          <p class="px-6 pb-5 text-gray-400 text-sm leading-relaxed">{{ $faq[1] }}</p>
        </div>
      </div>
      @endforeach
    </div>

  </div>
</section>

<section class="py-24 bg-gradient-to-br from-red-950 via-red-900 to-black relative overflow-hidden">
  <div class="absolute inset-0 opacity-10"
       style="background-image: linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px),
                                 linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px);
              background-size: 40px 40px;">
  </div>
  <div class="max-w-4xl mx-auto px-6 text-center relative z-10 reveal">
    <h2 class="text-5xl font-extrabold mb-6 leading-tight">
      Your Blood Could Be<br/>
      <span class="text-red-300">Someone's Last Hope.</span>
    </h2>
    <p class="text-red-200/70 text-lg mb-10 max-w-xl mx-auto">
      It takes 10 minutes. It saves a life. Register today and be ready when someone needs you most.
    </p>
    <div class="flex flex-wrap gap-4 justify-center">
      <a href="{{ url('/donor/register') }}"
         class="px-10 py-4 bg-white text-red-700 font-bold rounded-xl text-base hover:bg-red-50 transition-all duration-300 hover:scale-105 shadow-xl">
        Register as Donor
      </a>
      <a href="{{ url('/patient/register') }}"
         class="px-10 py-4 bg-transparent border-2 border-white/40 hover:border-white text-white font-bold rounded-xl text-base transition-all duration-300 hover:scale-105">
        I Need Blood
      </a>
    </div>
  </div>
</section>

<footer class="bg-gray-950 border-t border-gray-900 py-12">
  <div class="max-w-7xl mx-auto px-6">
    <div class="grid md:grid-cols-4 gap-8 mb-10">

      <!-- Brand -->
      <div class="col-span-2 md:col-span-1">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-9 h-9 bg-red-600 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 2C12 2 4 10.5 4 15a8 8 0 0016 0C20 10.5 12 2 12 2z"/>
            </svg>
          </div>
          <span class="font-bold text-white">Blood<span class="text-red-500">Link</span></span>
        </div>
        <p class="text-gray-500 text-sm leading-relaxed">Connecting donors, hospitals, and patients across Bangladesh.</p>
      </div>

      <!-- Quick links -->
      <div>
        <h4 class="text-sm font-semibold text-white mb-4">Quick Links</h4>
        <ul class="space-y-2 text-sm text-gray-500">
          <li><a href="#how-it-works" class="hover:text-red-400 transition-colors">How It Works</a></li>
          <li><a href="#blood-groups" class="hover:text-red-400 transition-colors">Blood Groups</a></li>
          <li><a href="#faq"          class="hover:text-red-400 transition-colors">FAQ</a></li>
        </ul>
      </div>

      <!-- Register -->
      <div>
        <h4 class="text-sm font-semibold text-white mb-4">Join Us</h4>
        <ul class="space-y-2 text-sm text-gray-500">
          <li><a href="{{ url('/donor/register') }}"    class="hover:text-red-400 transition-colors">Register as Donor</a></li>
          <li><a href="{{ url('/hospital/register') }}" class="hover:text-red-400 transition-colors">Register Hospital</a></li>
          <li><a href="{{ url('/patient/register') }}"  class="hover:text-red-400 transition-colors">Request Blood</a></li>
        </ul>
      </div>

      <!-- Contact -->
      <div>
        <h4 class="text-sm font-semibold text-white mb-4">Contact</h4>
        <ul class="space-y-2 text-sm text-gray-500">
        </ul>
      </div>

    </div>

    <div class="pt-8 border-t border-gray-900 text-center text-xs text-gray-700">
      © {{ date('Y') }} BloodLink — All Rights Reserved.
    </div>
  </div>
</footer>


<script>
//  Navbar scroll effect 
window.addEventListener('scroll', () => {
  const nav = document.getElementById('navbar');
  if (window.scrollY > 50) nav.classList.add('navbar-scrolled');
  else nav.classList.remove('navbar-scrolled');
});

// Typewriter effect 
const phrases = [
  'Connecting donors with patients in need.',
  'Real-time blood request matching.',
  'Emergency blood requests — handled fast.',
  'Because every second matters.',
];
let pi = 0, ci = 0, deleting = false;
const tw = document.getElementById('typewriter-target');
function typeLoop() {
  const phrase = phrases[pi];
  if (!deleting) {
    tw.textContent = phrase.slice(0, ++ci);
    if (ci === phrase.length) { deleting = true; setTimeout(typeLoop, 2000); return; }
  } else {
    tw.textContent = phrase.slice(0, --ci);
    if (ci === 0) { deleting = false; pi = (pi + 1) % phrases.length; }
  }
  setTimeout(typeLoop, deleting ? 40 : 70);
}
setTimeout(typeLoop, 1000);

// Floating particles 
const container = document.getElementById('particles');
for (let i = 0; i < 18; i++) {
  const p = document.createElement('div');
  const size = Math.random() * 6 + 3;
  p.className = 'particle';
  p.style.cssText = `
    width:${size}px; height:${size}px;
    left:${Math.random()*100}%;
    bottom:${Math.random()*20}%;
    animation-duration:${Math.random()*12+8}s;
    animation-delay:${Math.random()*5}s;
    opacity:${Math.random()*0.4+0.1};
  `;
  container.appendChild(p);
}

// Scroll reveal 
const observer = new IntersectionObserver((entries) => {
  entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); });
}, { threshold: 0.1 });
document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

//  Count-up animation 
const countObserver = new IntersectionObserver((entries) => {
  entries.forEach(e => {
    if (!e.isIntersecting) return;
    const el = e.target;
    const target = parseInt(el.dataset.count);
    const duration = 1800;
    const start = performance.now();
    function update(now) {
      const progress = Math.min((now - start) / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 3);
      el.textContent = Math.floor(eased * target).toLocaleString();
      if (progress < 1) requestAnimationFrame(update);
    }
    requestAnimationFrame(update);
    countObserver.unobserve(el);
  });
}, { threshold: 0.5 });
document.querySelectorAll('[data-count]').forEach(el => countObserver.observe(el));

// FAQ accordion
function toggleFaq(btn) {
  const answer = btn.nextElementSibling;
  const icon   = btn.querySelector('.faq-icon');
  const isOpen = answer.classList.contains('open');
  // close all
  document.querySelectorAll('.faq-answer').forEach(a => a.classList.remove('open'));
  document.querySelectorAll('.faq-icon').forEach(i => i.classList.remove('rotated'));
  // open clicked
  if (!isOpen) { answer.classList.add('open'); icon.classList.add('rotated'); }
}
</script>

</body>
</html>