<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Harmoni | BPS Kabupaten Tuban</title>
    
    {{-- Assets --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --bps-blue: #0058a8;
            --bps-light-blue: #eef6ff;
            --sidebar-width: 280px;
            --top-nav-height: 75px;
        }

        body { 
            background-color: #f8fafc; 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            overflow-x: hidden; 
            color: #1e293b;
        }

        /* --- SIDEBAR STYLING --- */
        .sidebar { 
            width: var(--sidebar-width); 
            height: 100vh; 
            position: fixed; 
            background: #ffffff; 
            border-right: 1px solid rgba(0,0,0,0.05); 
            padding: 30px 20px; 
            z-index: 1050; 
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 10px 0 30px rgba(0,0,0,0.02);
            overflow-y: auto;
        }

        .sidebar-logo-img {
            width: 100px; height: auto;
            display: block; margin: 0 auto;
            filter: drop-shadow(0 5px 8px rgba(0, 88, 168, 0.12));
        }

        .logo-container { padding: 0 15px 25px 15px; text-align: center; }

        .logo-text { 
            font-size: 0.95rem; letter-spacing: 2px; 
            color: var(--bps-blue); font-weight: 800; 
            margin-top: 15px; text-transform: uppercase;
        }

        .menu-divider { 
            font-size: 0.65rem; text-transform: uppercase; 
            color: #94a3b8; font-weight: 800; 
            margin: 25px 0 12px 15px; letter-spacing: 1.5px;
        }

        /* --- NAV LINK & DROPDOWN --- */
        .nav-link { 
            color: #64748b; padding: 12px 18px; 
            border-radius: 12px; margin-bottom: 4px; 
            display: flex; align-items: center;
            text-decoration: none; transition: all 0.3s; 
            font-size: 0.85rem; font-weight: 600;
            border: none; background: transparent; width: 100%;
            cursor: pointer;
        }

        .nav-link i:first-child { width: 25px; font-size: 1rem; }
        .nav-link .arrow { margin-left: auto; transition: transform 0.3s; font-size: 0.7rem; }
        .nav-link:not(.collapsed) .arrow { transform: rotate(180deg); }

        .nav-link:hover, .nav-link:focus {
            background: var(--bps-light-blue);
            color: var(--bps-blue);
        }

        .nav-link.active { 
            background: linear-gradient(135deg, var(--bps-blue) 0%, #007bff 100%) !important; 
            color: #ffffff !important; 
            box-shadow: 0 8px 15px rgba(0, 88, 168, 0.2);
        }

        .submenu { list-style: none; padding: 5px 0 5px 15px; margin: 0; }
        .submenu .nav-link {
            padding: 10px 15px;
            font-size: 0.8rem;
            font-weight: 500;
            margin-bottom: 2px;
        }

        /* --- NOTIFICATION BADGE --- */
        .badge-notif { 
            font-size: 0.6rem; 
            padding: 0.35em 0.65em; 
            font-weight: 800;
            box-shadow: 0 2px 5px rgba(220, 53, 69, 0.2);
            margin-left: auto;
        }

        /* --- MAIN CONTENT --- */
        .main-content { margin-left: var(--sidebar-width); min-height: 100vh; transition: all 0.4s; }
        .top-navbar { 
            background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px);
            height: var(--top-nav-height); padding: 0 40px; 
            border-bottom: 1px solid rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 1000;
        }

        .content-padding { padding: 35px 40px; }

        .avatar-box {
            width: 35px; height: 35px; background: var(--bps-blue); color: white;
            border-radius: 10px; display: flex; align-items: center; justify-content: center;
            font-weight: 700; margin-right: 12px;
        }

        .user-profile-badge {
            background: #fff; border: 1px solid #e2e8f0; padding: 5px 15px 5px 6px; 
            border-radius: 50px; display: flex; align-items: center; cursor: pointer;
            transition: all 0.3s;
        }
        .user-profile-badge:hover { border-color: var(--bps-blue); background: var(--bps-light-blue); }

        @media (max-width: 992px) {
            .sidebar { left: calc(var(--sidebar-width) * -1); }
            .sidebar.active { left: 0; }
            .main-content { margin-left: 0; }
            .top-navbar { padding: 0 20px; }
        }
    </style>
</head>
<body>

<div class="sidebar shadow-sm" id="sidebar">
    <div class="logo-container">
        <a href="{{ route('dashboard') }}">
            <img src="{{ asset('img/logo_harmoni.png') }}" alt="Logo Harmoni" class="sidebar-logo-img">
        </a>
        <h6 class="logo-text mb-0 text-primary">Harmoni <span class="text-dark" style="font-weight: 400;">BPS</span></h6>
        <div class="small text-muted fw-bold" style="font-size: 0.55rem; letter-spacing: 1.2px;">KABUPATEN TUBAN</div>
    </div>
    
    <nav class="mt-2 text-dark">
        {{-- SECTION: UTAMA --}}
        @if(Auth::user()->role != 'Admin')
            <div class="menu-divider mt-0">Menu Utama</div>
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large me-2"></i> <span>Dashboard</span>
            </a>
        @endif

        {{-- SECTION: PENUGASAN --}}
        @if(Auth::user()->role == 'Kepala' || Auth::user()->role == 'Katim')
            <div class="menu-divider">Perencanaan</div>
            <a href="{{ route('assignment.index') }}" class="nav-link {{ request()->routeIs('assignment.index') ? 'active' : '' }}">
                <i class="fas fa-clipboard-list me-2"></i> <span>Assignment</span>
            </a>

            <a href="{{ route('assignment.approvals.index') }}" class="nav-link {{ request()->routeIs('assignment.approvals.*') ? 'active' : '' }}">
                <i class="fas fa-file-signature me-2"></i> 
                <span>Persetujuan SPT</span>
                
                @php
                    $notifApproval = \App\Models\Agenda::where('mode_surat', 'generate')
                        ->where('status_approval', 'Pending')
                        ->where(function($q) {
                            $q->where('approver_id', Auth::id())
                            ->orWhere('reviewer_id', Auth::id());
                        });

                    if (Auth::user()->role === 'Kepala') {
                        $notifApproval->whereNotNull('reviewed_at');
                    } elseif (Auth::user()->role === 'Katim') {
                        $notifApproval->whereNull('reviewed_at');
                    }

                    $countNotif = $notifApproval->distinct('title')->count('title');
                @endphp

                @if($countNotif > 0)
                    <span class="badge bg-danger rounded-pill badge-notif">{{ $countNotif }}</span>
                @endif
            </a>
        @endif {{-- <--- INI YANG TADI KURANG, Mail! --}}

        {{-- SECTION: USER MANAGEMENT --}}
        @if(Auth::user()->role == 'Admin' || Auth::user()->role == 'Kepala')
            <div class="menu-divider">Pengaturan</div>
            <a href="{{ route('manajemen.anggota') }}" class="nav-link {{ request()->is('manajemen/anggota*') ? 'active' : '' }}">
                <i class="fas fa-users-cog me-2"></i> <span>Manajemen User</span>
            </a>
        @endif

        {{-- SECTION: MONITORING --}}
        @if(Auth::user()->role != 'Admin')
            <div class="menu-divider">Monitoring</div>
            <a href="{{ route('monitoring.index') }}" class="nav-link {{ request()->routeIs('monitoring.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-check me-2"></i> <span>Timeline Agenda</span>
            </a>
        @endif

        {{-- SECTION: ABSENSI (KHUSUS SUBBAGIAN UMUM) --}}
        @if(Auth::user()->team && Auth::user()->team->nama_tim === 'Subbagian Umum')
            <div class="menu-divider">Administrasi</div>
            <a href="{{ route('absensi.index') }}" class="nav-link {{ request()->routeIs('absensi.*') ? 'active' : '' }}">
                <i class="fas fa-user-check me-2"></i> <span>Gatekeeper Absensi</span>
            </a>
        @endif

        {{-- SECTION: PELAKSANAAN --}}
        @if(Auth::user()->role == 'Pegawai')
            <div class="menu-divider">Pelaksanaan</div>
            
            {{-- Tugas Lapangan --}}
            <button class="nav-link collapsed {{ request()->routeIs('task.*') || request()->routeIs('history.*') ? 'text-primary' : '' }}" 
                    data-bs-toggle="collapse" data-bs-target="#menuLapangan">
                <i class="fas fa-briefcase me-2"></i> 
                <span>Tugas Lapangan</span>
                <i class="fas fa-chevron-down arrow"></i>
            </button>
            <div class="collapse {{ request()->routeIs('task.*') || request()->routeIs('history.*') ? 'show' : '' }}" id="menuLapangan">
                <ul class="submenu">
                    <li>
                        <a href="{{ route('task.index') }}" class="nav-link small {{ request()->routeIs('task.index') ? 'active' : '' }}">
                            <i class="fas fa-tasks me-2"></i> 
                            <span>Daftar Tugas</span>
                            @if(isset($notifLapangan) && $notifLapangan > 0)
                                <span class="badge bg-danger rounded-pill badge-notif">{{ $notifLapangan }}</span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('history.index') }}" class="nav-link small {{ request()->routeIs('history.index') ? 'active' : '' }}">
                            <i class="fas fa-history me-2"></i> Riwayat Laporan
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Agenda Kegiatan --}}
            <button class="nav-link collapsed {{ request()->routeIs('meeting.*') ? 'text-primary' : '' }}" 
                    data-bs-toggle="collapse" data-bs-target="#menuRapat">
                <i class="fas fa-handshake me-2"></i> 
                <span>Kegiatan Dinas</span>
                <i class="fas fa-chevron-down arrow"></i>
            </button>
            <div class="collapse {{ request()->routeIs('meeting.*') ? 'show' : '' }}" id="menuRapat">
                <ul class="submenu">
                    <li>
                        <a href="{{ route('meeting.index') }}" class="nav-link small {{ request()->routeIs('meeting.index') ? 'active' : '' }}">
                            <i class="fas fa-calendar-day me-2"></i> 
                            <span>Jadwal Kegiatan</span>
                            @if(isset($notifKegiatan) && $notifKegiatan > 0)
                                <span class="badge bg-danger rounded-pill badge-notif">{{ $notifKegiatan }}</span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('meeting.history') }}" class="nav-link small {{ request()->routeIs('meeting.history') ? 'active' : '' }}">
                            <i class="fas fa-file-archive me-2"></i> Riwayat & Notulensi
                        </a>
                    </li>
                </ul>
            </div>
        @endif

            {{-- Menu Akses Super --}}
            @if(Auth::user()->role == 'Kepala' || Auth::user()->role == 'Katim' || Auth::user()->has_super_access == 1)
                <li class="nav-item">
                    <a href="{{ route('super.access.index') }}" class="nav-link {{ request()->routeIs('super.access.*') ? 'active' : '' }}">
                        <i class="fas fa-shield-alt me-2 text-danger"></i>
                        <span>Akses Super</span>
                    </a>
                </li>
            @endif

        {{-- SECTION: SYSTEM --}}
        <div class="menu-divider">Sistem</div>
        <a href="{{ route('panduan.index') }}" class="nav-link {{ request()->routeIs('panduan.*') ? 'active' : '' }}">
            <i class="fas fa-book me-2"></i> <span>Panduan Pengguna</span>
        </a>

        <a href="#" class="nav-link text-danger mt-3" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-power-off me-2"></i> <span>Keluar</span>
        </a>
    </nav>
</div>

<div class="main-content">
    <nav class="top-navbar d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <button class="btn btn-outline-secondary d-lg-none me-3 shadow-sm" id="btn-toggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="d-none d-md-block text-dark">
                <h6 class="fw-bold mb-0">Harmoni BPS Tuban</h6>
                <small class="text-muted" style="font-size: 0.7rem;">Sistem Manajemen Agenda & Rapat</small>
            </div>
        </div>
        
        <div class="dropdown">
            <div class="user-profile-badge shadow-sm" data-bs-toggle="dropdown">
                <div class="avatar-box shadow-sm">
                    {{ strtoupper(substr(Auth::user()->nama_lengkap ?? 'U', 0, 1)) }}
                </div>
                <div class="text-start d-none d-sm-block text-dark pe-2">
                    <div class="fw-bold lh-1 mb-1" style="font-size: 0.8rem;">{{ Auth::user()->nama_lengkap }}</div>
                    <div class="badge {{ Auth::user()->role == 'Pegawai' ? 'bg-secondary' : (Auth::user()->role == 'Katim' ? 'bg-info' : (Auth::user()->role == 'Kepala' ? 'bg-dark' : 'bg-primary')) }}" style="font-size: 0.55rem;">
                        {{ Auth::user()->role }}
                    </div>
                </div>
                <i class="fas fa-chevron-down text-muted" style="font-size: 0.7rem;"></i>
            </div>
            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 mt-2 p-2">
                <li class="px-3 py-2 small border-bottom mb-1">
                    <span class="text-muted d-block" style="font-size: 0.6rem;">Username:</span>
                    <span class="fw-bold text-primary">@ {{ Auth::user()->username }}</span>
                </li>
                <li>
                    <a class="dropdown-item py-2 rounded-3" href="{{ route('profile.edit') }}">
                        <i class="fas fa-user-cog me-2 text-primary"></i>Pengaturan Profil
                    </a>
                </li>
                <li><hr class="dropdown-divider opacity-50"></li>
                <li>
                    <a class="dropdown-item py-2 rounded-3 text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-power-off me-2"></i>Keluar
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>

    <div class="content-padding">
        @yield('content')
    </div>
</div>

{{-- Scripts --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $('#btn-toggle').click(function() {
        $('#sidebar').toggleClass('active');
    });

    @if(session('success'))
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", timer: 2500, showConfirmButton: false });
    @endif
    @if(session('error'))
        Swal.fire({ icon: 'error', title: 'Gagal!', text: "{{ session('error') }}", confirmButtonColor: '#0058a8' });
    @endif

    $(document).ready(function() {
        $('.collapse.show').each(function() {
            $(this).prev('.nav-link').removeClass('collapsed');
        });
    });
</script>
@stack('scripts')
</body>
</html>