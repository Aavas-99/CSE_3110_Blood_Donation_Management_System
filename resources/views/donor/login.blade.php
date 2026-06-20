<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Donor Login — BloodLink</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.bunny.net"/>
  <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet"/>
  <style>
    * { font-family: 'Inter', sans-serif; }

    :root {
      color-scheme: light;
    }

    body {
      background: #f8fafc;
      color: #0f172a;
    }

    .auth-bg {
      background: linear-gradient(135deg, #fff7f7 0%, #fff1f2 50%, #f8fafc 100%);
      background-size: 300% 300%;
      animation: bgShift 12s ease infinite;
    }
    @keyframes bgShift {
      0%,100% { background-position: 0% 50%; }
      50%      { background-position: 100% 50%; }
    }

    .grid-overlay {
      background-image:
        linear-gradient(rgba(239,68,68,0.06) 1px, transparent 1px),
        linear-gradient(90deg, rgba(239,68,68,0.06) 1px, transparent 1px);
      background-size: 48px 48px;
    }

    .bg-gray-950, .bg-gray-900, .bg-gray-800, .bg-black {
      background-color: #ffffff !important;
    }
    .text-white, .text-gray-300, .text-gray-400, .text-gray-500, .text-gray-600 {
      color: #334155 !important;
    }
    .border-white\/8, .border-white\/6, .border-gray-900, .border-gray-800 {
      border-color: #e2e8f0 !important;
    }

    .input-field {
      background: #fff;
      border: 1px solid #d1d5db;
      color: #0f172a;
      transition: all 0.25s ease;
      box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.04);
    }
    .input-field:focus {
      outline: none;
      border-color: rgba(239,68,68,0.55);
      background: #fff;
      box-shadow: 0 0 0 3px rgba(239,68,68,0.1);
    }
    .input-field::placeholder { color: #94a3b8; }
    .input-field.error { border-color: rgba(239,68,68,0.8); background: #fff7f7; }

    .field-label { color: #475569; font-size: 0.8rem; font-weight: 500; }

    .btn-submit {
      background: linear-gradient(135deg, #ef4444, #dc2626);
      transition: all 0.3s ease;
      box-shadow: 0 8px 18px rgba(239,68,68,0.18);
    }
    .btn-submit:hover {
      background: linear-gradient(135deg, #f87171, #ef4444);
      transform: translateY(-1px);
      box-shadow: 0 10px 24px rgba(239,68,68,0.22);
    }
    .btn-submit:active { transform: translateY(0); }
    .btn-submit:disabled { opacity: 0.6; transform: none; cursor: not-allowed; }

    .pulse-ring { animation: pulseRing 2.5s ease-out infinite; }
    @keyframes pulseRing {
      0%   { transform: scale(1);   opacity: 0.5; }
      70%  { transform: scale(1.7); opacity: 0; }
      100% { transform: scale(1.7); opacity: 0; }
    }

    .card-enter {
      animation: cardEnter 0.55s cubic-bezier(0.22,1,0.36,1) forwards;
    }
    @keyframes cardEnter {
      from { opacity: 0; transform: translateY(28px) scale(0.97); }
      to   { opacity: 1; transform: translateY(0) scale(1); }
    }

    .eye-btn { color: #94a3b8; cursor: pointer; transition: color 0.2s; }
    .eye-btn:hover { color: #ef4444; }

    .err-msg { font-size: 0.72rem; color: #f87171; margin-top: 4px; }

    .shake { animation: shake 0.4s ease; }
    @keyframes shake {
      0%,100% { transform: translateX(0); }
      20%      { transform: translateX(-8px); }
      40%      { transform: translateX(8px); }
      60%      { transform: translateX(-5px); }
      80%      { transform: translateX(5px); }
    }

    ::-webkit-scrollbar { width: 5px; }
    ::-webkit-scrollbar-track { background: #f8fafc; }
    ::-webkit-scrollbar-thumb { background: #f87171; border-radius: 3px; }
  </style>
</head>

<body class="auth-bg min-h-screen flex items-center justify-center px-4 relative overflow-x-hidden">

  <div class="grid-overlay fixed inset-0 pointer-events-none"></div>

  <!-- Back to home -->
  <a href="{{ url('/') }}"
     class="fixed top-5 left-5 flex items-center gap-2 text-sm text-slate-600 hover:text-red-500 transition-colors z-20 group">
    <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
    </svg>
    Back to Home
  </a>

  <div class="relative z-10 w-full max-w-md card-enter">

    <div class="bg-white/90 backdrop-blur-xl border border-slate-200 rounded-3xl shadow-xl overflow-hidden" id="loginCard">

      <!-- Top accent bar -->
      <div class="h-1 bg-gradient-to-r from-red-700 via-red-500 to-red-700"></div>

      <div class="p-8 md:p-10">

        <!-- Header -->
        <div class="text-center mb-10">
          <div class="inline-flex flex-col items-center gap-3">
            <div class="relative">
              <div class="w-14 h-14 bg-red-600 rounded-2xl flex items-center justify-center text-2xl shadow-lg shadow-red-200">
                💉
              </div>
              <div class="absolute inset-0 w-14 h-14 bg-red-600/40 rounded-2xl pulse-ring"></div>
            </div>
            <div>
              <h1 class="text-2xl font-bold text-slate-900">Donor Login</h1>
              <p class="text-slate-600 text-sm mt-1">Sign in to your BloodLink donor account</p>
            </div>
          </div>
        </div>

        {{-- Success message --}}
        @if(session('success'))
        <div class="mb-6 px-5 py-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm flex items-center gap-3">
          <span>✅</span> {{ session('success') }}
        </div>
        @endif

        {{-- Error message --}}
        @if(session('error'))
        <div class="mb-6 px-5 py-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm flex items-center gap-3" id="errorBanner">
          <span>⚠️</span> {{ session('error') }}
        </div>
        @endif

        {{-- Validation errors --}}
        @if($errors->any())
        <div class="mb-6 px-5 py-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
          <div class="flex items-center gap-2 font-medium mb-1"><span>⚠️</span> Please fix:</div>
          <ul class="list-disc list-inside space-y-1 text-red-700/90 mt-1">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif

        <!-- Login Form -->
        <form action="{{ route('donor.login.submit') }}" method="POST" id="loginForm" class="space-y-5">
          @csrf

          <!-- Email -->
          <div>
            <label class="field-label block mb-1.5">Email Address <span class="text-red-500">*</span></label>
            <div class="relative">
              <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
              </span>
              <input type="email" name="email" value="{{ old('email') }}"
                     placeholder="donor@example.com"
                     autofocus
                     class="input-field w-full pl-11 pr-4 py-3 rounded-xl text-sm {{ $errors->has('email') ? 'error' : '' }}"/>
            </div>
            @error('email')<p class="err-msg">⚠ {{ $message }}</p>@enderror
          </div>

          <!-- Password -->
          <div>
            <label class="field-label block mb-1.5">Password <span class="text-red-500">*</span></label>
            <div class="relative">
              <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
              </span>
              <input type="password" name="password" id="password"
                     placeholder="Enter your password"
                     class="input-field w-full pl-11 pr-12 py-3 rounded-xl text-sm {{ $errors->has('password') ? 'error' : '' }}"/>
              <button type="button" onclick="toggleEye()" class="eye-btn absolute right-4 top-1/2 -translate-y-1/2">
                <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
              </button>
            </div>
            @error('password')<p class="err-msg">⚠ {{ $message }}</p>@enderror
          </div>

          <!-- Submit -->
          <div class="pt-2">
            <button type="submit" id="submitBtn"
                    class="btn-submit w-full py-3.5 text-white font-semibold rounded-xl text-sm flex items-center justify-center gap-3">
              <span id="btnText">Sign In</span>
            </button>
          </div>

          <!-- Divider -->
          <div class="flex items-center gap-3 py-1">
            <div class="flex-1 h-px bg-slate-200"></div>
            <span class="text-xs text-slate-500">or</span>
            <div class="flex-1 h-px bg-slate-200"></div>
          </div>

          <!-- Register link -->
          <p class="text-center text-sm text-gray-600">
            Not registered yet?
            <a href="{{ route('donor.register') }}" class="text-red-600 hover:text-red-500 font-medium transition-colors">
              Register as donor
            </a>
          </p>

        </form>
      </div>
    </div>

    <!-- Role switcher -->
    <div class="mt-5 bg-slate-50 border border-slate-200 rounded-2xl p-4">
      <p class="text-center text-xs text-slate-600 mb-3">Sign in as a different role</p>
      <div class="grid grid-cols-2 gap-2">
        <a href="{{ route('hospital.login') }}"
           class="flex items-center justify-center gap-2 py-2.5 px-4 bg-white hover:bg-red-50 border border-slate-200 hover:border-red-300 rounded-xl text-xs text-slate-600 hover:text-red-500 transition-all duration-200">
          🏥 Hospital Login
        </a>
        <a href="{{ url('/patient/login') }}"
           class="flex items-center justify-center gap-2 py-2.5 px-4 bg-white hover:bg-red-50 border border-slate-200 hover:border-red-300 rounded-xl text-xs text-slate-600 hover:text-red-500 transition-all duration-200">
          🩺 Patient Login
        </a>
      </div>
    </div>

    <div class="pt-8 border-t border-slate-200 text-center text-xs text-slate-500">
      © {{ date('Y') }} BloodLink — All Rights Reserved.
    </div>

    <br>

  </div>

  <script>
  // Eye toggle
  function toggleEye() {
    const input = document.getElementById('password');
    const icon  = document.getElementById('eyeIcon');
    const isPass = input.type === 'password';
    input.type = isPass ? 'text' : 'password';
    icon.innerHTML = isPass
      ? `<path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>`
      : `<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
  }

  // Submit loading
  document.getElementById('loginForm').addEventListener('submit', function() {
    const btn  = document.getElementById('submitBtn');
    const text = document.getElementById('btnText');
    btn.disabled = true;
    text.textContent = 'Signing in...';
  });

  // Shake on error
  window.addEventListener('load', () => {
    if (document.getElementById('errorBanner')) {
      document.getElementById('loginCard').classList.add('shake');
      setTimeout(() => document.getElementById('loginCard').classList.remove('shake'), 500);
    }
  });
  </script>

</body>
</html>