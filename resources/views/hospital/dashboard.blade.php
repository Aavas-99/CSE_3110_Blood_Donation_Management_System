<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Hospital Dashboard — BloodLink</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.bunny.net" />
  <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />
  <style>
    * { font-family: 'Inter', sans-serif; }
    body { background: #0a0a0a; color: #e5e5e5; }

    .sidebar {
      width: 260px;
      background: #111111;
      border-right: 1px solid #1f1f1f;
    }
    .sidebar-link {
      display: flex; align-items: center; gap: 12px;
      padding: 10px 16px; border-radius: 10px;
      color: #a1a1aa; font-size: 0.875rem; font-weight: 500;
      transition: all 0.2s ease;
    }
    .sidebar-link.active {
      background: rgba(239,68,68,0.08);
      color: #ef4444;
      border-left: 3px solid #ef4444;
    }

    .dash-card {
      background: #141414;
      border: 1px solid #1f1f1f;
      border-radius: 16px;
    }

    .data-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .data-table th {
      padding: 12px 16px; text-align: left;
      font-size: 0.75rem; font-weight: 600; text-transform: uppercase;
      letter-spacing: 0.05em; color: #71717a;
      border-bottom: 1px solid #27272a;
      background: #0f0f0f;
    }
    .data-table td {
      padding: 14px 16px; font-size: 0.875rem;
      border-bottom: 1px solid #1f1f1f;
      color: #d4d4d8;
    }
    .data-table tr:hover td { background: rgba(255,255,255,0.02); }
    .data-table tr:last-child td { border-bottom: none; }

    .badge {
      display: inline-flex; align-items: center;
      padding: 4px 10px; border-radius: 9999px;
      font-size: 0.75rem; font-weight: 600;
      border: 1px solid;
    }

    .eligible     { color: #4ade80; }
    .not-eligible { color: #f87171; }

    .toast {
      position: fixed; top: 20px; right: 20px; z-index: 200;
      padding: 14px 20px; border-radius: 12px; font-size: 0.875rem;
      display: flex; align-items: center; gap: 10px;
      animation: toastIn 0.4s ease, toastOut 0.4s ease 3.5s forwards;
      box-shadow: 0 10px 40px rgba(0,0,0,0.4);
    }
    @keyframes toastIn  { from { opacity:0; transform:translateX(40px); } to { opacity:1; transform:translateX(0); } }
    @keyframes toastOut { to   { opacity:0; transform:translateX(40px); } }
    .toast-success { background:#14532d; border:1px solid #22c55e; color:#86efac; }
    .toast-error   { background:#7f1d1d; border:1px solid #ef4444; color:#fca5a5; }

    ::-webkit-scrollbar { width:6px; height:6px; }
    ::-webkit-scrollbar-track { background:#0a0a0a; }
    ::-webkit-scrollbar-thumb { background:#3f3f46; border-radius:3px; }
    ::-webkit-scrollbar-thumb:hover { background:#ef4444; }

    @media (max-width: 1024px) {
      .sidebar { position:fixed; left:-260px; top:0; bottom:0; z-index:50; transition: left 0.3s; }
      .sidebar.open { left:0; }
      .sidebar-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:40; }
      .sidebar-overlay.open { display:block; }
    }
  </style>
</head>
<body class="min-h-screen flex">

  <div id="sidebarOverlay" class="sidebar-overlay lg:hidden" onclick="toggleSidebar()"></div>

  <!-- Sidebar -->
  <aside id="sidebar" class="sidebar fixed lg:sticky top-0 h-screen flex flex-col z-50">
    <div class="p-6">
      <a href="{{ url('/') }}" class="flex items-center gap-3">
        <div class="w-9 h-9 bg-red-600 rounded-xl flex items-center justify-center">
          <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2C12 2 4 10.5 4 15a8 8 0 0016 0C20 10.5 12 2 12 2z"/>
          </svg>
        </div>
        <span class="font-bold text-white">Blood<span class="text-red-500">Link</span></span>
      </a>
    </div>

    <nav class="flex-1 px-4 space-y-1 overflow-y-auto">
      <div class="text-xs font-semibold text-gray-600 uppercase tracking-wider px-4 mb-2 mt-2">Dashboard</div>
      <span class="sidebar-link active">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        Donors
      </span>
    </nav>

    <div class="p-4 border-t border-gray-800">
      <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-gray-900/50">
        <div class="w-9 h-9 bg-red-950/60 border border-red-900/50 rounded-lg flex items-center justify-center text-lg">🏥</div>
        <div class="overflow-hidden">
          <p class="text-sm font-medium text-white truncate">{{ session('hospital_name') }}</p>
          <p class="text-xs text-gray-500 truncate">{{ session('hospital_email') }}</p>
        </div>
      </div>
      <form action="{{ route('hospital.logout') }}" method="POST" class="mt-3">
        @csrf
        <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 text-sm text-gray-400 hover:text-red-400 hover:bg-red-950/20 rounded-xl transition-all">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
          </svg>
          Sign Out
        </button>
      </form>
    </div>
  </aside>

  <!-- Main -->
  <main class="flex-1 min-h-screen overflow-y-auto">
    <header class="sticky top-0 z-30 bg-[#0a0a0a]/90 backdrop-blur-xl border-b border-gray-800 px-6 py-4 flex items-center justify-between">
      <div class="flex items-center gap-4">
        <button onclick="toggleSidebar()" class="lg:hidden p-2 text-gray-400 hover:text-white rounded-lg hover:bg-gray-800">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
        <h1 class="text-lg font-semibold text-white">Donor Management</h1>
      </div>
      <div class="flex items-center gap-3">
        <span class="text-xs text-gray-500">{{ now()->format('M d, Y') }}</span>
        <div class="w-8 h-8 bg-red-600 rounded-lg flex items-center justify-center text-sm font-bold text-white">
          {{ strtoupper(substr(session('hospital_name'), 0, 1)) }}
        </div>
      </div>
    </header>

    <div class="p-6 max-w-[1400px] mx-auto">

      @if(session('success'))
      <div class="toast toast-success"><span>✅</span> {{ session('success') }}</div>
      @endif
      @if(session('error'))
      <div class="toast toast-error"><span>⚠️</span> {{ session('error') }}</div>
      @endif

      <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-white">
          Donors
          <span class="ml-2 text-sm font-normal text-gray-500">({{ count($donors) }} total)</span>
        </h2>
      </div>

      <!-- Table -->
      <div class="dash-card overflow-hidden">
        <div class="overflow-x-auto">
          <table class="data-table">
            <thead>
              <tr>
                <th>Donor</th>
                <th>Blood Group</th>
                <th>District</th>
                <th>Phone</th>
                <th>Last Donated</th>
                <th>Eligibility</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @forelse($donors as $donor)
              @php
                $lastDonated = $donor->last_donated_at
                    ? \Carbon\Carbon::parse($donor->last_donated_at)
                    : null;
                $isEligible = !$lastDonated || $lastDonated->diffInDays(now()) >= 90;
                $daysLeft   = $isEligible ? 0 : 90 - (int) $lastDonated->diffInDays(now());
              @endphp
              <tr>
                <td>
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-gray-800 rounded-lg flex items-center justify-center text-sm font-bold text-gray-400">
                      {{ strtoupper(substr($donor->name, 0, 1)) }}
                    </div>
                    <div>
                      <div class="font-medium text-white">{{ $donor->name }}</div>
                      <div class="text-xs text-gray-500">{{ $donor->email }}</div>
                    </div>
                  </div>
                </td>
                <td><span class="font-bold text-red-400">{{ $donor->blood_group }}</span></td>
                <td class="text-gray-400">{{ $donor->district }}</td>
                <td class="text-gray-400">{{ $donor->phone }}</td>
                <td class="text-gray-400">
                  @if($lastDonated)
                    {{ $lastDonated->diffForHumans() }}
                  @else
                    <span class="text-gray-600">Never</span>
                  @endif
                </td>
                <td>
                  <span class="text-sm font-medium {{ $isEligible ? 'eligible' : 'not-eligible' }}">
                    {{ $isEligible ? '✓ Eligible' : '✗ ' . $daysLeft . 'd left' }}
                  </span>
                </td>
                <td>
                  <span class="badge {{ $donor->status === 'available'
                      ? 'bg-green-950/60 text-green-400 border-green-900'
                      : 'bg-gray-800 text-gray-400 border-gray-700' }}">
                    {{ ucfirst($donor->status) }}
                  </span>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="7" class="text-center py-12 text-gray-500">No donors registered yet</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-800 text-xs text-gray-500">
          Showing {{ count($donors) }} record(s)
        </div>
      </div>

    </div>
  </main>

  <script>
  function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sidebarOverlay').classList.toggle('open');
  }
  </script>

</body>
</html>