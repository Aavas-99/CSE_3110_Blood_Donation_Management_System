<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Hospital Registration — BloodLink</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.bunny.net"/>
  <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet"/>
  <style>
    * { font-family: 'Inter', sans-serif; }

    /* Animated gradient background */
    .auth-bg {
      background: linear-gradient(135deg, #0a0a0a 0%, #1a0000 50%, #0a0a0a 100%);
      background-size: 300% 300%;
      animation: bgShift 12s ease infinite;
    }
    @keyframes bgShift {
      0%,100% { background-position: 0% 50%; }
      50%      { background-position: 100% 50%; }
    }

    /* Grid overlay */
    .grid-overlay {
      background-image:
        linear-gradient(rgba(239,68,68,0.04) 1px, transparent 1px),
        linear-gradient(90deg, rgba(239,68,68,0.04) 1px, transparent 1px);
      background-size: 48px 48px;
    }

    /* Floating blood drop */
    .drop-float { animation: dropFloat 4s ease-in-out infinite; }
    @keyframes dropFloat {
      0%,100% { transform: translateY(0); }
      50%      { transform: translateY(-14px); }
    }

    /* Input focus ring */
    .input-field {
      background: rgba(255,255,255,0.04);
      border: 1px solid rgba(255,255,255,0.1);
      color: #fff;
      transition: all 0.25s ease;
    }
    .input-field:focus {
      outline: none;
      border-color: rgba(239,68,68,0.7);
      background: rgba(239,68,68,0.05);
      box-shadow: 0 0 0 3px rgba(239,68,68,0.12);
    }
    .input-field::placeholder { color: rgba(255,255,255,0.25); }
    .input-field.error {
      border-color: rgba(239,68,68,0.8);
      background: rgba(239,68,68,0.07);
    }

    /* Label */
    .field-label { color: rgba(255,255,255,0.7); font-size: 0.8rem; font-weight: 500; }

    /* Password rule indicators */
    .rule { display: flex; align-items: center; gap: 6px; font-size: 0.75rem; color: rgba(255,255,255,0.35); transition: color 0.3s; }
    .rule.pass { color: #4ade80; }
    .rule.fail { color: rgba(255,255,255,0.25); }
    .rule-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; flex-shrink: 0; }

    /* Strength bar */
    .strength-bar { height: 3px; border-radius: 2px; transition: all 0.4s ease; }

    /* Submit button */
    .btn-submit {
      background: linear-gradient(135deg, #dc2626, #991b1b);
      transition: all 0.3s ease;
      box-shadow: 0 4px 20px rgba(220,38,38,0.35);
    }
    .btn-submit:hover {
      background: linear-gradient(135deg, #ef4444, #dc2626);
      transform: translateY(-1px);
      box-shadow: 0 8px 28px rgba(220,38,38,0.45);
    }
    .btn-submit:active { transform: translateY(0); }

    /* Pulse ring on logo */
    .pulse-ring { animation: pulseRing 2.5s ease-out infinite; }
    @keyframes pulseRing {
      0%   { transform: scale(1);   opacity: 0.5; }
      70%  { transform: scale(1.7); opacity: 0; }
      100% { transform: scale(1.7); opacity: 0; }
    }

    /* Eye toggle */
    .eye-btn { color: rgba(255,255,255,0.3); cursor: pointer; transition: color 0.2s; }
    .eye-btn:hover { color: rgba(239,68,68,0.8); }

    /* Card slide in */
    .card-enter {
      animation: cardEnter 0.6s cubic-bezier(0.22,1,0.36,1) forwards;
    }
    @keyframes cardEnter {
      from { opacity: 0; transform: translateY(30px) scale(0.97); }
      to   { opacity: 1; transform: translateY(0)    scale(1); }
    }

    /* Select field */
    select.input-field option { background: #1a1a1a; color: #fff; }

    /* Error text */
    .err-msg { font-size: 0.72rem; color: #f87171; margin-top: 4px; display: flex; align-items: center; gap-x: 4px; }

    /* Success banner */
    .success-banner { background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.3); border-radius: 12px; }

    /* Scrollbar */
    ::-webkit-scrollbar { width: 5px; }
    ::-webkit-scrollbar-track { background: #0a0a0a; }
    ::-webkit-scrollbar-thumb { background: #dc2626; border-radius: 3px; }
  </style>
</head>

<body class="auth-bg min-h-screen flex items-center justify-center py-10 px-4 relative overflow-x-hidden">

  <!-- Grid overlay -->
  <div class="grid-overlay fixed inset-0 pointer-events-none"></div>

  <!-- Back to home -->
  <a href="{{ url('/') }}"
     class="fixed top-5 left-5 flex items-center gap-2 text-sm text-gray-500 hover:text-red-400 transition-colors z-20 group">
    <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
    </svg>
    Back to Home
  </a>

  <!-- Main card -->
  <div class="relative z-10 w-full max-w-2xl card-enter">

    <!-- Card -->
    <div class="bg-gray-950/80 backdrop-blur-xl border border-white/8 rounded-3xl shadow-2xl overflow-hidden">

      <!-- Top accent bar -->
      <div class="h-1 bg-gradient-to-r from-red-700 via-red-500 to-red-700"></div>

      <div class="p-8 md:p-10">

        <!-- Header -->
        <div class="text-center mb-10">
          <div class="inline-flex flex-col items-center gap-3">
            <div class="relative">
              <div class="w-14 h-14 bg-red-600 rounded-2xl flex items-center justify-center text-2xl shadow-lg shadow-red-900/50">
                🏥
              </div>
              <div class="absolute inset-0 w-14 h-14 bg-red-600/40 rounded-2xl pulse-ring"></div>
            </div>
            <div>
              <h1 class="text-2xl font-bold text-white">Hospital Registration</h1>
              <p class="text-gray-500 text-sm mt-1">Join BloodLink as a verified hospital</p>
            </div>
          </div>
        </div>

        {{-- Success message --}}
        @if(session('success'))
        <div class="success-banner px-5 py-4 mb-6 text-green-400 text-sm flex items-center gap-3">
          <span class="text-lg">✅</span>
          {{ session('success') }}
        </div>
        @endif

        {{-- Global error --}}
        @if($errors->any())
        <div class="mb-6 px-5 py-4 bg-red-950/50 border border-red-800/60 rounded-xl text-red-400 text-sm">
          <div class="flex items-center gap-2 font-medium mb-2">
            <span>⚠️</span> Please fix the following errors:
          </div>
          <ul class="list-disc list-inside space-y-1 text-red-400/80">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif

        {{-- Registration Form --}}
        <form action="{{ route('hospital.register.submit') }}" method="POST" class="space-y-5" id="regForm">
          @csrf

          {{-- Row 1: Hospital Name --}}
          <div>
            <label class="field-label block mb-1.5">Hospital Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}"
                   placeholder="e.g. Dhaka Medical College Hospital"
                   class="input-field w-full px-4 py-3 rounded-xl text-sm {{ $errors->has('name') ? 'error' : '' }}"/>
            @error('name')<p class="err-msg">⚠ {{ $message }}</p>@enderror
          </div>

          {{-- Row 2: Email + Phone --}}
          <div class="grid md:grid-cols-2 gap-4">
            <div>
              <label class="field-label block mb-1.5">Email Address <span class="text-red-500">*</span></label>
              <input type="email" name="email" value="{{ old('email') }}"
                     placeholder="hospital@example.com"
                     class="input-field w-full px-4 py-3 rounded-xl text-sm {{ $errors->has('email') ? 'error' : '' }}"/>
              @error('email')<p class="err-msg">⚠ {{ $message }}</p>@enderror
            </div>
            <div>
              <label class="field-label block mb-1.5">Phone Number <span class="text-red-500">*</span></label>
              <input type="tel" name="phone" value="{{ old('phone') }}"
                     placeholder="01XXXXXXXXX"
                     class="input-field w-full px-4 py-3 rounded-xl text-sm {{ $errors->has('phone') ? 'error' : '' }}"/>
              @error('phone')<p class="err-msg">⚠ {{ $message }}</p>@enderror
            </div>
          </div>

          {{-- Row 3: District + Website --}}
          <div class="grid md:grid-cols-2 gap-4">
            <div>
              <label class="field-label block mb-1.5">District <span class="text-red-500">*</span></label>
              <select name="district"
                      class="input-field w-full px-4 py-3 rounded-xl text-sm {{ $errors->has('district') ? 'error' : '' }}">
                <option value="" disabled {{ old('district') ? '' : 'selected' }}>Select district</option>
                @php
                $districts = ['Bagerhat','Bandarban','Barguna','Barishal','Bhola','Bogura',
                  'Brahmanbaria','Chandpur','Chapai Nawabganj','Chattogram','Chuadanga','Cox\'s Bazar',
                  'Cumilla','Dhaka','Dinajpur','Faridpur','Feni','Gaibandha','Gazipur','Gopalganj',
                  'Habiganj','Jamalpur','Jessore','Jhalokathi','Jhenaidah','Joypurhat','Khagrachari',
                  'Khulna','Kishoreganj','Kurigram','Kushtia','Lakshmipur','Lalmonirhat','Madaripur',
                  'Magura','Manikganj','Meherpur','Moulvibazar','Munshiganj','Mymensingh','Naogaon',
                  'Narail','Narayanganj','Narsingdi','Natore','Netrokona','Nilphamari','Noakhali',
                  'Pabna','Panchagarh','Patuakhali','Pirojpur','Rajbari','Rajshahi','Rangamati',
                  'Rangpur','Satkhira','Shariatpur','Sherpur','Sirajganj','Sunamganj','Sylhet',
                  'Tangail','Thakurgaon'];
                @endphp
                @foreach($districts as $d)
                  <option value="{{ $d }}" {{ old('district') == $d ? 'selected' : '' }}>{{ $d }}</option>
                @endforeach
              </select>
              @error('district')<p class="err-msg">⚠ {{ $message }}</p>@enderror
            </div>
            <div>
              <label class="field-label block mb-1.5">Website <span class="text-gray-600 text-xs">(optional)</span></label>
              <input type="url" name="website" value="{{ old('website') }}"
                     placeholder="https://yourhospital.com"
                     class="input-field w-full px-4 py-3 rounded-xl text-sm"/>
            </div>
          </div>

          {{-- Row 4: Address --}}
          <div>
            <label class="field-label block mb-1.5">Full Address <span class="text-red-500">*</span></label>
            <textarea name="address" rows="3"
                      placeholder="Building, road, area..."
                      class="input-field w-full px-4 py-3 rounded-xl text-sm resize-none {{ $errors->has('address') ? 'error' : '' }}">{{ old('address') }}</textarea>
            @error('address')<p class="err-msg">⚠ {{ $message }}</p>@enderror
          </div>

          {{-- Divider --}}
          <div class="flex items-center gap-3 py-1">
            <div class="flex-1 h-px bg-white/8"></div>
            <span class="text-xs text-gray-600 font-medium">Security</span>
            <div class="flex-1 h-px bg-white/8"></div>
          </div>

          {{-- Row 5: Password --}}
          <div>
            <label class="field-label block mb-1.5">Password <span class="text-red-500">*</span></label>
            <div class="relative">
              <input type="password" name="password" id="password"
                     placeholder="Create a strong password"
                     oninput="checkPassword(this.value)"
                     class="input-field w-full px-4 py-3 pr-12 rounded-xl text-sm {{ $errors->has('password') ? 'error' : '' }}"/>
              <button type="button" onclick="toggleEye('password','eye1')"
                      class="eye-btn absolute right-4 top-1/2 -translate-y-1/2">
                <svg id="eye1" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
              </button>
            </div>
            @error('password')<p class="err-msg">⚠ {{ $message }}</p>@enderror

            <!-- Strength bar -->
            <div class="mt-2 grid grid-cols-4 gap-1.5">
              <div id="bar1" class="strength-bar bg-white/10 rounded-full"></div>
              <div id="bar2" class="strength-bar bg-white/10 rounded-full"></div>
              <div id="bar3" class="strength-bar bg-white/10 rounded-full"></div>
              <div id="bar4" class="strength-bar bg-white/10 rounded-full"></div>
            </div>
            <p id="strength-label" class="text-xs mt-1 text-gray-600"></p>

            <!-- Rules -->
            <div class="mt-3 grid grid-cols-2 gap-y-1.5 gap-x-4">
              <div class="rule" id="r-len"><div class="rule-dot"></div> At least 8 characters</div>
              <div class="rule" id="r-upp"><div class="rule-dot"></div> One uppercase letter</div>
              <div class="rule" id="r-low"><div class="rule-dot"></div> One lowercase letter</div>
              <div class="rule" id="r-num"><div class="rule-dot"></div> One number</div>
              <div class="rule" id="r-sym"><div class="rule-dot"></div> One special character</div>
            </div>
          </div>

          {{-- Row 6: Confirm Password --}}
          <div>
            <label class="field-label block mb-1.5">Confirm Password <span class="text-red-500">*</span></label>
            <div class="relative">
              <input type="password" name="password_confirmation" id="password_confirmation"
                     placeholder="Re-enter your password"
                     oninput="checkMatch()"
                     class="input-field w-full px-4 py-3 pr-12 rounded-xl text-sm"/>
              <button type="button" onclick="toggleEye('password_confirmation','eye2')"
                      class="eye-btn absolute right-4 top-1/2 -translate-y-1/2">
                <svg id="eye2" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
              </button>
            </div>
            <p id="match-msg" class="text-xs mt-1.5"></p>
          </div>

          {{-- Submit --}}
          <div class="pt-2">
            <button type="submit" id="submitBtn"
                    class="btn-submit w-full py-3.5 px-6 text-white font-semibold rounded-xl text-sm flex items-center justify-center gap-3">
              <span id="btnText">Register Hospital</span>
            </button>
          </div>

          {{-- Login link --}}
          <p class="text-center text-sm text-gray-600 pt-1">
            Already registered?
            <a href="{{ route('hospital.login') }}" class="text-red-400 hover:text-red-300 font-medium transition-colors">
              Sign In
            </a>
          </p>

        </form>
      </div>
    </div>

    <!-- Bottom note -->
    <div class="pt-8 border-t border-gray-900 text-center text-xs text-gray-700">
      © {{ date('Y') }} BloodLink — All Rights Reserved.
    </div>

  </div>

  <!-- ── JavaScript ─────────────────────────────────── -->
  <script>
  // ── Eye toggle ──────────────────────────────────────
  function toggleEye(inputId, eyeId) {
    const input = document.getElementById(inputId);
    const isText = input.type === 'text';
    input.type = isText ? 'password' : 'text';
    const svg = document.getElementById(eyeId);
    svg.innerHTML = isText
      ? `<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
         <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`
      : `<path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>`;
  }

  // ── Password strength + rules ───────────────────────
  function checkPassword(val) {
    const rules = {
      'r-len': val.length >= 8,
      'r-upp': /[A-Z]/.test(val),
      'r-low': /[a-z]/.test(val),
      'r-num': /[0-9]/.test(val),
      'r-sym': /[@$!%*#?&^_\-]/.test(val),
    };

    let score = 0;
    for (const [id, pass] of Object.entries(rules)) {
      const el = document.getElementById(id);
      el.classList.toggle('pass', pass);
      el.classList.toggle('fail', !pass);
      if (pass) score++;
    }

    // Strength bar colours
    const colors = ['', '#ef4444', '#f97316', '#eab308', '#22c55e'];
    const labels = ['', 'Weak', 'Fair', 'Good', 'Strong'];
    for (let i = 1; i <= 4; i++) {
      const bar = document.getElementById('bar' + i);
      bar.style.background = i <= score ? colors[score] : 'rgba(255,255,255,0.08)';
    }
    const label = document.getElementById('strength-label');
    label.textContent = val.length ? labels[score] || 'Strong' : '';
    label.style.color = colors[score] || '#9ca3af';

    checkMatch();
  }

  // ── Password match check ────────────────────────────
  function checkMatch() {
    const p1 = document.getElementById('password').value;
    const p2 = document.getElementById('password_confirmation').value;
    const msg = document.getElementById('match-msg');
    if (!p2) { msg.textContent = ''; return; }
    if (p1 === p2) {
      msg.textContent = '✓ Passwords match';
      msg.style.color = '#4ade80';
    } else {
      msg.textContent = '✗ Passwords do not match';
      msg.style.color = '#f87171';
    }
  }

  // ── Submit loading state ────────────────────────────
  document.getElementById('regForm').addEventListener('submit', function() {
    const btn  = document.getElementById('submitBtn');
    const text = document.getElementById('btnText');
    btn.disabled = true;
    btn.style.opacity = '0.7';
    text.textContent = 'Registering...';
  });
  </script>

</body>
</html>