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

    /* Sidebar */
    .sidebar {
      width: 260px;
      background: #111111;
      border-right: 1px solid #1f1f1f;
      transition: all 0.3s ease;
    }
    .sidebar-link {
      display: flex; align-items: center; gap: 12px;
      padding: 10px 16px; border-radius: 10px;
      color: #a1a1aa; font-size: 0.875rem; font-weight: 500;
      transition: all 0.2s ease; cursor: pointer;
    }
    .sidebar-link:hover, .sidebar-link.active {
      background: rgba(239,68,68,0.08);
      color: #ef4444;
    }
    .sidebar-link.active { border-left: 3px solid #ef4444; }

    /* Cards */
    .dash-card {
      background: #141414;
      border: 1px solid #1f1f1f;
      border-radius: 16px;
      transition: all 0.3s ease;
    }
    .dash-card:hover {
      border-color: #2a2a2a;
      transform: translateY(-2px);
    }

    /* Stat card */
    .stat-card {
      background: linear-gradient(135deg, #141414 0%, #1a1a1a 100%);
      border: 1px solid #1f1f1f;
      border-radius: 16px;
      padding: 24px;
      position: relative;
      overflow: hidden;
    }
    .stat-card::before {
      content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    }
    .stat-card.red::before { background: linear-gradient(90deg, #dc2626, #ef4444); }
    .stat-card.blue::before { background: linear-gradient(90deg, #2563eb, #3b82f6); }
    .stat-card.green::before { background: linear-gradient(90deg, #16a34a, #22c55e); }
    .stat-card.purple::before { background: linear-gradient(90deg, #7c3aed, #a855f7); }

    /* Tables */
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

    /* Badges */
    .badge {
      display: inline-flex; align-items: center; gap: 4px;
      padding: 4px 10px; border-radius: 9999px;
      font-size: 0.75rem; font-weight: 600;
      border: 1px solid;
    }

    /* Buttons */
    .btn-sm {
      padding: 6px 14px; border-radius: 8px; font-size: 0.75rem; font-weight: 600;
      transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 4px;
    }
    .btn-primary { background: #dc2626; color: white; }
    .btn-primary:hover { background: #ef4444; }
    .btn-success { background: #16a34a; color: white; }
    .btn-success:hover { background: #22c55e; }
    .btn-danger { background: #991b1b; color: white; }
    .btn-danger:hover { background: #dc2626; }
    .btn-ghost { background: transparent; border: 1px solid #27272a; color: #a1a1aa; }
    .btn-ghost:hover { border-color: #ef4444; color: #ef4444; }

    /* Inputs */
    .form-input {
      background: #1a1a1a; border: 1px solid #27272a; border-radius: 10px;
      padding: 8px 14px; color: #e4e4e7; font-size: 0.875rem;
      transition: all 0.2s ease;
    }
    .form-input:focus {
      outline: none; border-color: #dc2626;
      box-shadow: 0 0 0 3px rgba(220,38,38,0.1);
    }
    .form-input::placeholder { color: #52525b; }

    /* Scrollbar */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: #0a0a0a; }
    ::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 3px; }
    ::-webkit-scrollbar-thumb:hover { background: #ef4444; }

    /* Tab content */
    .tab-content { display: none; }
    .tab-content.active { display: block; animation: fadeIn 0.3s ease; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }

    /* Eligibility */
    .eligible { color: #4ade80; }
    .not-eligible { color: #f87171; }

    /* Progress bar */
    .progress-bar { height: 6px; border-radius: 3px; background: #27272a; overflow: hidden; }
    .progress-fill { height: 100%; border-radius: 3px; transition: width 0.5s ease; }

    /* Modal */
    .modal-overlay {
      position: fixed; inset: 0; background: rgba(0,0,0,0.7);
      backdrop-filter: blur(4px); z-index: 100;
      display: none; align-items: center; justify-content: center;
    }
    .modal-overlay.open { display: flex; }
    .modal-card {
      background: #141414; border: 1px solid #27272a;
      border-radius: 20px; padding: 28px; width: 100%; max-width: 480px;
      animation: modalIn 0.3s ease;
    }
    @keyframes modalIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }

    /* Toast */
    .toast {
      position: fixed; top: 20px; right: 20px; z-index: 200;
      padding: 14px 20px; border-radius: 12px; font-size: 0.875rem;
      display: flex; align-items: center; gap: 10px;
      animation: toastIn 0.4s ease, toastOut 0.4s ease 3.5s forwards;
      box-shadow: 0 10px 40px rgba(0,0,0,0.4);
    }
    @keyframes toastIn { from { opacity: 0; transform: translateX(40px); } to { opacity: 1; transform: translateX(0); } }
    @keyframes toastOut { to { opacity: 0; transform: translateX(40px); } }
    .toast-success { background: #14532d; border: 1px solid #22c55e; color: #86efac; }
    .toast-error { background: #7f1d1d; border: 1px solid #ef4444; color: #fca5a5; }

    /* Mobile sidebar */
    @media (max-width: 1024px) {
      .sidebar { position: fixed; left: -260px; top: 0; bottom: 0; z-index: 50; }
      .sidebar.open { left: 0; }
      .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 40; }
      .sidebar-overlay.open { display: block; }
    }
  </style>
</head>
<body class="min-h-screen flex">

  <!-- Mobile sidebar overlay -->
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
      <a href="#overview" onclick="switchTab('overview')" class="sidebar-link active" data-tab="overview">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
        Overview
      </a>
      <a href="#requests" onclick="switchTab('requests')" class="sidebar-link" data-tab="requests">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        Blood Requests
        @if(($requestStatusSummary['pending'] ?? 0) > 0)
        <span class="ml-auto bg-red-600 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $requestStatusSummary['pending'] ?? 0 }}</span>
        @endif
      </a>
      <a href="#donors" onclick="switchTab('donors')" class="sidebar-link" data-tab="donors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        Donors
      </a>
      <a href="#patients" onclick="switchTab('patients')" class="sidebar-link" data-tab="patients">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        Patients
      </a>
      <a href="#donations" onclick="switchTab('donations')" class="sidebar-link" data-tab="donations">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
        Donations
      </a>
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
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
          Sign Out
        </button>
      </form>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 min-h-screen overflow-y-auto">
    <!-- Top bar -->
    <header class="sticky top-0 z-30 bg-[#0a0a0a]/90 backdrop-blur-xl border-b border-gray-800 px-6 py-4 flex items-center justify-between">
      <div class="flex items-center gap-4">
        <button onclick="toggleSidebar()" class="lg:hidden p-2 text-gray-400 hover:text-white rounded-lg hover:bg-gray-800">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <h1 class="text-lg font-semibold text-white" id="pageTitle">Dashboard Overview</h1>
      </div>
      <div class="flex items-center gap-3">
        <span class="text-xs text-gray-500">{{ now()->format('M d, Y') }}</span>
        <div class="w-8 h-8 bg-red-600 rounded-lg flex items-center justify-center text-sm font-bold">{{ strtoupper(substr(session('hospital_name'), 0, 1)) }}</div>
      </div>
    </header>

    <div class="p-6 max-w-[1400px] mx-auto">

      {{-- Flash Messages --}}
      @if(session('success'))
      <div class="toast toast-success mb-6">
        <span>✅</span> {{ session('success') }}
      </div>
      @endif
      @if(session('error'))
      <div class="toast toast-error mb-6">
        <span>⚠️</span> {{ session('error') }}
      </div>
      @endif

      <!-- ════════════════════════════════════════════════
           TAB: OVERVIEW
      ════════════════════════════════════════════════ -->
      <div id="tab-overview" class="tab-content active">

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
          <div class="stat-card red">
            <div class="flex items-center justify-between mb-3">
              <span class="text-gray-400 text-sm">Total Requests</span>
              <span class="text-2xl">🩸</span>
            </div>
            <div class="text-3xl font-bold text-white">{{ number_format($totalRequests) }}</div>
            <div class="mt-2 text-xs text-gray-500">All blood requests in system</div>
          </div>
          <div class="stat-card blue">
            <div class="flex items-center justify-between mb-3">
              <span class="text-gray-400 text-sm">Total Donations</span>
              <span class="text-2xl">💉</span>
            </div>
            <div class="text-3xl font-bold text-white">{{ number_format($totalDonations) }}</div>
            <div class="mt-2 text-xs text-gray-500">Recorded at your hospital</div>
          </div>
          <div class="stat-card green">
            <div class="flex items-center justify-between mb-3">
              <span class="text-gray-400 text-sm">Patients Served</span>
              <span class="text-2xl">❤️</span>
            </div>
            <div class="text-3xl font-bold text-white">{{ number_format($totalPatientsServed) }}</div>
            <div class="mt-2 text-xs text-gray-500">Unique patients helped</div>
          </div>
          <div class="stat-card purple">
            <div class="flex items-center justify-between mb-3">
              <span class="text-gray-400 text-sm">Pending Requests</span>
              <span class="text-2xl">⏳</span>
            </div>
            <div class="text-3xl font-bold text-white">{{ $requestStatusSummary['pending'] ?? 0 }}</div>
            <div class="mt-2 text-xs text-gray-500">Awaiting your action</div>
          </div>
        </div>

        <!-- Request Status Summary -->
        <div class="dash-card p-6 mb-8">
          <h3 class="text-lg font-semibold text-white mb-5">Request Status Summary</h3>
          <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            @php
            $statusConfig = [
              'pending'   => ['label' => 'Pending',   'color' => 'bg-yellow-500', 'text' => 'text-yellow-400'],
              'approved'  => ['label' => 'Approved',  'color' => 'bg-blue-500',   'text' => 'text-blue-400'],
              'rejected'  => ['label' => 'Rejected',  'color' => 'bg-red-500',    'text' => 'text-red-400'],
              'completed' => ['label' => 'Completed', 'color' => 'bg-green-500',  'text' => 'text-green-400'],
              'cancelled' => ['label' => 'Cancelled', 'color' => 'bg-gray-500',   'text' => 'text-gray-400'],
            ];
            $totalAll = array_sum($requestStatusSummary);
            @endphp
            @foreach($statusConfig as $key => $cfg)
            @php $count = $requestStatusSummary[$key] ?? 0; $pct = $totalAll > 0 ? round(($count / $totalAll) * 100) : 0; @endphp
            <div class="text-center">
              <div class="text-2xl font-bold {{ $cfg['text'] }}">{{ $count }}</div>
              <div class="text-xs text-gray-500 mb-2">{{ $cfg['label'] }}</div>
              <div class="progress-bar">
                <div class="progress-fill {{ $cfg['color'] }}" style="width: {{ $pct }}%"></div>
              </div>
              <div class="text-xs text-gray-600 mt-1">{{ $pct }}%</div>
            </div>
            @endforeach
          </div>
        </div>

        <!-- Recent Activity -->
        {{-- $requests and $donations are plain arrays from DB::select() --}}
        <div class="grid lg:grid-cols-2 gap-6">
          <div class="dash-card p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Recent Requests</h3>
            @php $recentRequests = array_slice($requests, 0, 5); @endphp
            @forelse($recentRequests as $req)
            <div class="flex items-center justify-between py-3 border-b border-gray-800 last:border-0">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-950/50 border border-red-900/30 rounded-lg flex items-center justify-center">
                  <span class="text-red-400 font-bold text-sm">{{ $req->blood_group ?? '?' }}</span>
                </div>
                <div>
                  <p class="text-sm font-medium text-white">{{ $req->patient_name ?? 'Unknown' }}</p>
                  <p class="text-xs text-gray-500">{{ $req->quantity_units }} units</p>
                </div>
              </div>
              @php
                $statusColors = [
                  'pending'   => 'bg-yellow-950/60 text-yellow-400 border-yellow-900',
                  'approved'  => 'bg-blue-950/60 text-blue-400 border-blue-900',
                  'rejected'  => 'bg-red-950/60 text-red-400 border-red-900',
                  'completed' => 'bg-green-950/60 text-green-400 border-green-900',
                  'cancelled' => 'bg-gray-800 text-gray-400 border-gray-700',
                ];
                $sc = $statusColors[$req->status] ?? 'bg-gray-800 text-gray-400 border-gray-700';
              @endphp
              <span class="badge {{ $sc }}">{{ ucfirst($req->status) }}</span>
            </div>
            @empty
            <p class="text-gray-500 text-sm text-center py-8">No requests yet</p>
            @endforelse
          </div>

          <div class="dash-card p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Recent Donations</h3>
            @php $recentDonations = array_slice($donations, 0, 5); @endphp
            @forelse($recentDonations as $don)
            <div class="flex items-center justify-between py-3 border-b border-gray-800 last:border-0">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-950/50 border border-blue-900/30 rounded-lg flex items-center justify-center text-lg">💉</div>
                <div>
                  <p class="text-sm font-medium text-white">{{ $don->donor_name ?? 'Unknown' }}</p>
                  <p class="text-xs text-gray-500">{{ $don->quantity_units }} units · {{ $don->donation_date }}</p>
                </div>
              </div>
              @php
                $dc = $statusColors[$don->status] ?? 'bg-gray-800 text-gray-400 border-gray-700';
              @endphp
              <span class="badge {{ $dc }}">{{ ucfirst($don->status) }}</span>
            </div>
            @empty
            <p class="text-gray-500 text-sm text-center py-8">No donations yet</p>
            @endforelse
          </div>
        </div>
      </div>

      <!-- ════════════════════════════════════════════════
           TAB: BLOOD REQUESTS
      ════════════════════════════════════════════════ -->
      <div id="tab-requests" class="tab-content">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
          <h2 class="text-xl font-bold text-white">Blood Requests</h2>
          <div class="flex items-center gap-2">
            <span class="text-xs text-gray-500">{{ count($requests) }} total</span>
          </div>
        </div>

        <!-- Filters -->
        <div class="dash-card p-4 mb-5">
          <form method="GET" class="flex flex-wrap items-end gap-3">
            <input type="hidden" name="tab" value="requests">
            <div class="flex-1 min-w-[200px]">
              <label class="block text-xs text-gray-500 mb-1.5">Search</label>
              <input type="text" name="req_search" value="{{ request('req_search') }}" placeholder="Patient name or Request ID..." class="form-input w-full" />
            </div>
            <div>
              <label class="block text-xs text-gray-500 mb-1.5">Status</label>
              <select name="req_status" class="form-input">
                <option value="all">All Status</option>
                <option value="pending"   {{ request('req_status') == 'pending'   ? 'selected' : '' }}>Pending</option>
                <option value="approved"  {{ request('req_status') == 'approved'  ? 'selected' : '' }}>Approved</option>
                <option value="rejected"  {{ request('req_status') == 'rejected'  ? 'selected' : '' }}>Rejected</option>
                <option value="completed" {{ request('req_status') == 'completed' ? 'selected' : '' }}>Completed</option>
              </select>
            </div>
            <div>
              <label class="block text-xs text-gray-500 mb-1.5">Urgency</label>
              <select name="req_urgency" class="form-input">
                <option value="all">All Urgency</option>
                <option value="critical" {{ request('req_urgency') == 'critical' ? 'selected' : '' }}>Critical</option>
                <option value="urgent"   {{ request('req_urgency') == 'urgent'   ? 'selected' : '' }}>Urgent</option>
                <option value="normal"   {{ request('req_urgency') == 'normal'   ? 'selected' : '' }}>Normal</option>
              </select>
            </div>
            <div>
              <label class="block text-xs text-gray-500 mb-1.5">Sort</label>
              <select name="req_sort" class="form-input">
                <option value="newest"     {{ request('req_sort') == 'newest'     ? 'selected' : '' }}>Newest First</option>
                <option value="urgency"    {{ request('req_sort') == 'urgency'    ? 'selected' : '' }}>Urgency (High-Low)</option>
                <option value="units_desc" {{ request('req_sort') == 'units_desc' ? 'selected' : '' }}>Units (High-Low)</option>
                <option value="units_asc"  {{ request('req_sort') == 'units_asc'  ? 'selected' : '' }}>Units (Low-High)</option>
              </select>
            </div>
            <button type="submit" class="btn-sm btn-primary">Apply</button>
            <a href="?tab=requests" class="btn-sm btn-ghost">Reset</a>
          </form>
        </div>

        <!-- Table -->
        <div class="dash-card overflow-hidden">
          <div class="overflow-x-auto">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Req ID</th>
                  <th>Patient</th>
                  <th>Blood Group</th>
                  <th>Units</th>
                  <th>Urgency</th>
                  <th>Status</th>
                  <th>Created</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($requests as $req)
                @php
                  $urgencyColors = [
                    'critical' => 'bg-red-950/60 text-red-400 border-red-900',
                    'urgent'   => 'bg-orange-950/60 text-orange-400 border-orange-900',
                    'normal'   => 'bg-gray-800 text-gray-400 border-gray-700',
                  ];
                  $uc = $urgencyColors[$req->urgency_level] ?? 'bg-gray-800 text-gray-400 border-gray-700';
                  $sc = $statusColors[$req->status] ?? 'bg-gray-800 text-gray-400 border-gray-700';
                  $createdAt = $req->created_at ? \Carbon\Carbon::parse($req->created_at) : null;
                @endphp
                <tr>
                  <td class="font-mono text-xs text-gray-400">#{{ $req->req_id }}</td>
                  <td>
                    <div class="font-medium text-white">{{ $req->patient_name ?? 'N/A' }}</div>
                    <div class="text-xs text-gray-500">{{ $req->patient_phone ?? '' }}</div>
                  </td>
                  <td><span class="font-bold text-red-400">{{ $req->blood_group }}</span></td>
                  <td>{{ $req->quantity_units }}</td>
                  <td><span class="badge {{ $uc }}">{{ ucfirst($req->urgency_level) }}</span></td>
                  <td><span class="badge {{ $sc }}">{{ ucfirst($req->status) }}</span></td>
                  <td class="text-xs text-gray-500">
                    @if($createdAt)
                      {{ $createdAt->format('M d, Y') }}<br>{{ $createdAt->format('h:i A') }}
                    @else
                      —
                    @endif
                  </td>
                  <td>
                    <div class="flex items-center gap-1">
                      @if($req->status == 'pending')
                      <form action="{{ route('hospital.request.approve', $req->req_id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="btn-sm btn-success" title="Approve">✓</button>
                      </form>
                      <form action="{{ route('hospital.request.reject', $req->req_id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="btn-sm btn-danger" title="Reject">✕</button>
                      </form>
                      @elseif($req->status == 'approved')
                      <form action="{{ route('hospital.request.complete', $req->req_id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="btn-sm btn-primary" title="Mark Complete">✓ Complete</button>
                      </form>
                      @else
                      <span class="text-xs text-gray-600">—</span>
                      @endif
                    </div>
                  </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-12 text-gray-500">No requests found</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="px-6 py-4 border-t border-gray-800 text-xs text-gray-500">
            Showing {{ count($requests) }} record(s)
          </div>
        </div>
      </div>

      <!-- ════════════════════════════════════════════════
           TAB: DONORS
      ════════════════════════════════════════════════ -->
      <div id="tab-donors" class="tab-content">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
          <h2 class="text-xl font-bold text-white">Donor Management</h2>
          <button onclick="openModal('scheduleDonationModal')" class="btn-sm btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Schedule Donation
          </button>
        </div>

        <!-- Filters -->
        <div class="dash-card p-4 mb-5">
          <form method="GET" class="flex flex-wrap items-end gap-3">
            <input type="hidden" name="tab" value="donors">
            <div class="flex-1 min-w-[200px]">
              <label class="block text-xs text-gray-500 mb-1.5">Search</label>
              <input type="text" name="donor_search" value="{{ request('donor_search') }}" placeholder="Name, phone, email, or blood group..." class="form-input w-full" />
            </div>
            <div>
              <label class="block text-xs text-gray-500 mb-1.5">Blood Group</label>
              <select name="donor_blood" class="form-input">
                <option value="all">All</option>
                @foreach($bloodGroups as $bg)
                <option value="{{ $bg }}" {{ request('donor_blood') == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="block text-xs text-gray-500 mb-1.5">District</label>
              <select name="donor_district" class="form-input">
                <option value="all">All</option>
                @foreach($districts as $d)
                <option value="{{ $d }}" {{ request('donor_district') == $d ? 'selected' : '' }}>{{ $d }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="block text-xs text-gray-500 mb-1.5">Availability</label>
              <select name="donor_status" class="form-input">
                <option value="all">All</option>
                <option value="available"   {{ request('donor_status') == 'available'   ? 'selected' : '' }}>Available</option>
                <option value="unavailable" {{ request('donor_status') == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
              </select>
            </div>
            <div>
              <label class="block text-xs text-gray-500 mb-1.5">Sort</label>
              <select name="donor_sort" class="form-input">
                <option value="name_asc"      {{ request('donor_sort') == 'name_asc'      ? 'selected' : '' }}>Name A-Z</option>
                <option value="name_desc"     {{ request('donor_sort') == 'name_desc'     ? 'selected' : '' }}>Name Z-A</option>
                <option value="last_donation" {{ request('donor_sort') == 'last_donation' ? 'selected' : '' }}>Last Donation</option>
              </select>
            </div>
            <button type="submit" class="btn-sm btn-primary">Apply</button>
            <a href="?tab=donors" class="btn-sm btn-ghost">Reset</a>
          </form>
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
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($donors as $donor)
                @php
                  // Eligibility: donors can donate again after 90 days
                  $lastDonated = $donor->last_donated_at ? \Carbon\Carbon::parse($donor->last_donated_at) : null;
                  $isEligible  = !$lastDonated || $lastDonated->diffInDays(now()) >= 90;
                  $eligibilityText = $isEligible ? 'Eligible' : 'Not Eligible';
                  $daysLeft = $isEligible ? 0 : 90 - $lastDonated->diffInDays(now());
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
                    <span class="badge {{ $donor->status == 'available' ? 'bg-green-950/60 text-green-400 border-green-900' : 'bg-gray-800 text-gray-400 border-gray-700' }}">
                      {{ ucfirst($donor->status) }}
                    </span>
                  </td>
                  <td>
                    <button onclick="selectDonorForSchedule({{ $donor->donor_id }}, '{{ addslashes($donor->name) }}')" class="btn-sm btn-ghost" title="Schedule">📅</button>
                  </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-12 text-gray-500">No donors found</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="px-6 py-4 border-t border-gray-800 text-xs text-gray-500">
            Showing {{ count($donors) }} record(s)
          </div>
        </div>
      </div>

      <!-- ════════════════════════════════════════════════
           TAB: PATIENTS
      ════════════════════════════════════════════════ -->
      <div id="tab-patients" class="tab-content">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-xl font-bold text-white">Patient Records</h2>
          <span class="text-xs text-gray-500">{{ count($patients) }} total</span>
        </div>

        <!-- Filters -->
        <div class="dash-card p-4 mb-5">
          <form method="GET" class="flex flex-wrap items-end gap-3">
            <input type="hidden" name="tab" value="patients">
            <div class="flex-1 min-w-[200px]">
              <label class="block text-xs text-gray-500 mb-1.5">Search</label>
              <input type="text" name="patient_search" value="{{ request('patient_search') }}" placeholder="Name, phone, or email..." class="form-input w-full" />
            </div>
            <div>
              <label class="block text-xs text-gray-500 mb-1.5">Blood Group</label>
              <select name="patient_blood" class="form-input">
                <option value="all">All</option>
                @foreach($bloodGroups as $bg)
                <option value="{{ $bg }}" {{ request('patient_blood') == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="block text-xs text-gray-500 mb-1.5">District</label>
              <select name="patient_district" class="form-input">
                <option value="all">All</option>
                @foreach($districts as $d)
                <option value="{{ $d }}" {{ request('patient_district') == $d ? 'selected' : '' }}>{{ $d }}</option>
                @endforeach
              </select>
            </div>
            <button type="submit" class="btn-sm btn-primary">Apply</button>
            <a href="?tab=patients" class="btn-sm btn-ghost">Reset</a>
          </form>
        </div>

        <!-- Table -->
        <div class="dash-card overflow-hidden">
          <div class="overflow-x-auto">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Patient</th>
                  <th>Blood Group</th>
                  <th>District</th>
                  <th>Phone</th>
                  <th>Requests</th>
                  <th>Joined</th>
                </tr>
              </thead>
              <tbody>
                @forelse($patients as $patient)
                @php
                  $joinedAt = $patient->created_at ? \Carbon\Carbon::parse($patient->created_at) : null;
                @endphp
                <tr>
                  <td>
                    <div class="flex items-center gap-3">
                      <div class="w-9 h-9 bg-purple-950/50 border border-purple-900/30 rounded-lg flex items-center justify-center text-sm font-bold text-purple-400">
                        {{ strtoupper(substr($patient->name, 0, 1)) }}
                      </div>
                      <div>
                        <div class="font-medium text-white">{{ $patient->name }}</div>
                        <div class="text-xs text-gray-500">{{ $patient->email }}</div>
                      </div>
                    </div>
                  </td>
                  <td><span class="font-bold text-red-400">{{ $patient->blood_group }}</span></td>
                  <td class="text-gray-400">{{ $patient->district }}</td>
                  <td class="text-gray-400">{{ $patient->phone }}</td>
                  <td>
                    <span class="badge bg-blue-950/60 text-blue-400 border-blue-900">{{ $patient->request_count ?? 0 }} requests</span>
                  </td>
                  <td class="text-xs text-gray-500">{{ $joinedAt ? $joinedAt->format('M d, Y') : '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-12 text-gray-500">No patients found</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="px-6 py-4 border-t border-gray-800 text-xs text-gray-500">
            Showing {{ count($patients) }} record(s)
          </div>
        </div>
      </div>

      <!-- ════════════════════════════════════════════════
           TAB: DONATIONS
      ════════════════════════════════════════════════ -->
      <div id="tab-donations" class="tab-content">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-xl font-bold text-white">Donation Management</h2>
          <span class="text-xs text-gray-500">{{ count($donations) }} total</span>
        </div>

        <!-- Filters -->
        <div class="dash-card p-4 mb-5">
          <form method="GET" class="flex flex-wrap items-end gap-3">
            <input type="hidden" name="tab" value="donations">
            <div class="flex-1 min-w-[200px]">
              <label class="block text-xs text-gray-500 mb-1.5">Search</label>
              <input type="text" name="donation_search" value="{{ request('donation_search') }}" placeholder="Donor or patient name..." class="form-input w-full" />
            </div>
            <div>
              <label class="block text-xs text-gray-500 mb-1.5">Status</label>
              <select name="donation_status" class="form-input">
                <option value="all">All Status</option>
                <option value="scheduled"  {{ request('donation_status') == 'scheduled'  ? 'selected' : '' }}>Scheduled</option>
                <option value="completed"  {{ request('donation_status') == 'completed'  ? 'selected' : '' }}>Completed</option>
                <option value="cancelled"  {{ request('donation_status') == 'cancelled'  ? 'selected' : '' }}>Cancelled</option>
              </select>
            </div>
            <div>
              <label class="block text-xs text-gray-500 mb-1.5">Sort</label>
              <select name="donation_sort" class="form-input">
                <option value="newest"       {{ request('donation_sort') == 'newest'       ? 'selected' : '' }}>Newest First</option>
                <option value="oldest"       {{ request('donation_sort') == 'oldest'       ? 'selected' : '' }}>Oldest First</option>
                <option value="quantity_desc" {{ request('donation_sort') == 'quantity_desc' ? 'selected' : '' }}>Units (High-Low)</option>
                <option value="quantity_asc"  {{ request('donation_sort') == 'quantity_asc'  ? 'selected' : '' }}>Units (Low-High)</option>
              </select>
            </div>
            <button type="submit" class="btn-sm btn-primary">Apply</button>
            <a href="?tab=donations" class="btn-sm btn-ghost">Reset</a>
          </form>
        </div>

        <!-- Table -->
        <div class="dash-card overflow-hidden">
          <div class="overflow-x-auto">
            <table class="data-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Donor</th>
                  <th>Patient</th>
                  <th>Date</th>
                  <th>Units</th>
                  <th>Status</th>
                  <th>Request</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($donations as $don)
                @php
                  $donDate = $don->donation_date ? \Carbon\Carbon::parse($don->donation_date) : null;
                  $donSc   = $statusColors[$don->status] ?? 'bg-gray-800 text-gray-400 border-gray-700';
                @endphp
                <tr>
                  <td class="font-mono text-xs text-gray-400">#{{ $don->donation_id }}</td>
                  <td>
                    <div class="font-medium text-white">{{ $don->donor_name ?? 'N/A' }}</div>
                    <div class="text-xs text-gray-500">{{ $don->donor_blood ?? '' }}</div>
                  </td>
                  <td>
                    @if(!empty($don->patient_name))
                    <div class="font-medium text-white">{{ $don->patient_name }}</div>
                    <div class="text-xs text-gray-500">{{ $don->patient_blood ?? '' }}</div>
                    @else
                    <span class="text-gray-600 text-sm">—</span>
                    @endif
                  </td>
                  <td class="text-gray-400">{{ $donDate ? $donDate->format('M d, Y') : '—' }}</td>
                  <td class="font-medium text-white">{{ $don->quantity_units }}</td>
                  <td><span class="badge {{ $donSc }}">{{ ucfirst($don->status) }}</span></td>
                  <td>
                    @if(!empty($don->request_id))
                    <span class="text-xs font-mono text-gray-400">#{{ $don->request_id }}</span>
                    @else
                    <span class="text-gray-600 text-xs">Walk-in</span>
                    @endif
                  </td>
                  <td>
                    <div class="flex items-center gap-1">
                      @if($don->status == 'scheduled')
                      <form action="{{ route('hospital.donation.complete', $don->donation_id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="btn-sm btn-success" title="Complete">✓</button>
                      </form>
                      <form action="{{ route('hospital.donation.cancel', $don->donation_id) }}" method="POST" class="inline" onsubmit="return confirm('Cancel this donation?')">
                        @csrf
                        <button type="submit" class="btn-sm btn-danger" title="Cancel">✕</button>
                      </form>
                      @else
                      <span class="text-xs text-gray-600">—</span>
                      @endif
                    </div>
                  </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-12 text-gray-500">No donations found</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="px-6 py-4 border-t border-gray-800 text-xs text-gray-500">
            Showing {{ count($donations) }} record(s)
          </div>
        </div>
      </div>

    </div>
  </main>

  <!-- ════════════════════════════════════════════════
       MODAL: Schedule Donation
  ════════════════════════════════════════════════ -->
  <div id="scheduleDonationModal" class="modal-overlay">
    <div class="modal-card">
      <div class="flex items-center justify-between mb-5">
        <h3 class="text-lg font-bold text-white">Schedule Blood Donation</h3>
        <button onclick="closeModal('scheduleDonationModal')" class="text-gray-500 hover:text-white">✕</button>
      </div>
      <form action="{{ route('hospital.donation.schedule') }}" method="POST">
        @csrf
        <div class="space-y-4">
          <div>
            <label class="block text-xs text-gray-500 mb-1.5">Donor <span class="text-red-500">*</span></label>
            <select name="donor_id" id="modalDonorId" required class="form-input w-full">
              <option value="">Select donor</option>
              @foreach($donors as $d)
              <option value="{{ $d->donor_id }}">{{ $d->name }} ({{ $d->blood_group }})</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block text-xs text-gray-500 mb-1.5">Patient (optional)</label>
            <select name="patient_id" class="form-input w-full">
              <option value="">Select patient</option>
              @foreach($patients as $p)
              <option value="{{ $p->patient_id }}">{{ $p->name }} ({{ $p->blood_group }})</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block text-xs text-gray-500 mb-1.5">Linked Request (optional)</label>
            <select name="req_id" class="form-input w-full">
              <option value="">Select request</option>
              @foreach($requests as $r)
              @if($r->status == 'pending' || $r->status == 'approved')
              <option value="{{ $r->req_id }}">#{{ $r->req_id }} — {{ $r->patient_name ?? 'N/A' }} ({{ $r->blood_group }})</option>
              @endif
              @endforeach
            </select>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs text-gray-500 mb-1.5">Date <span class="text-red-500">*</span></label>
              <input type="date" name="donation_date" required min="{{ date('Y-m-d') }}" class="form-input w-full" />
            </div>
            <div>
              <label class="block text-xs text-gray-500 mb-1.5">Units <span class="text-red-500">*</span></label>
              <input type="number" name="quantity_units" required min="1" max="10" value="1" class="form-input w-full" />
            </div>
          </div>
        </div>
        <div class="flex items-center justify-end gap-3 mt-6">
          <button type="button" onclick="closeModal('scheduleDonationModal')" class="btn-sm btn-ghost">Cancel</button>
          <button type="submit" class="btn-sm btn-primary">Schedule</button>
        </div>
      </form>
    </div>
  </div>

  <script>
  // ── Tab Switching ───────────────────────────────────
  function switchTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.sidebar-link').forEach(el => el.classList.remove('active'));
    document.getElementById('tab-' + tabName).classList.add('active');
    document.querySelector('[data-tab="' + tabName + '"]').classList.add('active');

    const titles = {
      overview:  'Dashboard Overview',
      requests:  'Blood Request Management',
      donors:    'Donor Management',
      patients:  'Patient Records',
      donations: 'Donation Management'
    };
    document.getElementById('pageTitle').textContent = titles[tabName] || tabName;

    const url = new URL(window.location);
    url.searchParams.set('tab', tabName);
    window.history.replaceState({}, '', url);
  }

  // ── Sidebar Toggle ──────────────────────────────────
  function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sidebarOverlay').classList.toggle('open');
  }

  // ── Modal ───────────────────────────────────────────
  function openModal(id) { document.getElementById(id).classList.add('open'); }
  function closeModal(id) { document.getElementById(id).classList.remove('open'); }

  function selectDonorForSchedule(donorId, donorName) {
    openModal('scheduleDonationModal');
    document.getElementById('modalDonorId').value = donorId;
  }

  // ── Init tab from URL ─────────────────────────────
  window.addEventListener('DOMContentLoaded', () => {
    const tab = new URL(window.location).searchParams.get('tab');
    if (tab && document.getElementById('tab-' + tab)) {
      switchTab(tab);
    }
  });

  // ── Close modal on overlay click ────────────────────
  document.querySelectorAll('.modal-overlay').forEach(el => {
    el.addEventListener('click', e => { if (e.target === el) el.classList.remove('open'); });
  });
  </script>

</body>
</html>