<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'GuruKuu'); ?> - Sistem Penilaian Guru</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <style>
        html { scroll-behavior: smooth; }
        :root {
            --primary: #003366; --primary-light: #004080; --secondary: #FFC107;
            --accent: #00A86B; --bg-light: #f5f7fa; --text-dark: #1a1a2e; --text-muted: #64748b; --border: #e2e8f0;
        }
        * { font-family: 'Inter', sans-serif; box-sizing: border-box; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        
        .navbar-custom {
            background: rgba(255,255,255,0.98); backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border); padding: 0.75rem 0;
            transition: all 0.3s; z-index: 1030;
        }
        .navbar-brand-custom { font-weight: 800; font-size: 1.5rem; color: var(--primary); margin-right: 2rem; text-decoration: none; }
        .nav-menu-center { flex: 1; display: flex; justify-content: center; gap: 2rem; }
        .nav-link-custom {
            font-weight: 600; color: var(--text-dark); text-decoration: none;
            padding: 0.5rem 0; position: relative; transition: all 0.2s; font-size: 0.95rem; cursor: pointer;
        }
        .nav-link-custom:hover { color: var(--primary); }
        .nav-link-custom.active { color: var(--primary); font-weight: 700; }
        .nav-link-custom.active::after {
            content: ''; position: absolute; bottom: -4px; left: 0; right: 0; height: 2.5px; background: var(--primary); border-radius: 2px;
        }
        .nav-actions { display: flex; align-items: center; margin-left: auto; gap: 0.75rem; }
        .btn-masuk {
            background: var(--primary); color: white; padding: 0.5rem 1.25rem;
            border-radius: 8px; font-weight: 600; font-size: 0.9rem; border: none; text-decoration: none; transition: all 0.3s;
        }
        .btn-masuk:hover { background: var(--primary-light); color: white; transform: translateY(-1px); }
        .btn-dashboard {
            background: var(--accent); color: white; padding: 0.5rem 1.25rem;
            border-radius: 8px; font-weight: 600; font-size: 0.9rem; border: none; text-decoration: none; transition: all 0.3s;
        }
        .btn-dashboard:hover { background: #008f5a; color: white; transform: translateY(-1px); }
        .btn-logout {
            background: #dc3545; color: white; padding: 0.5rem 1rem;
            border-radius: 8px; font-weight: 600; font-size: 0.85rem; border: none; cursor: pointer; transition: all 0.3s;
        }
        .btn-logout:hover { background: #c82333; color: white; }

        .hero-section {
            background: linear-gradient(rgba(0,51,102,0.85), rgba(0,51,102,0.7)), url('https://images.unsplash.com/photo-1562774053-701939374585?w=1920') center/cover;
            color: white; padding: 160px 0 120px; min-height: 600px; display: flex; align-items: center;
        }
        .hero-title { font-size: 3rem; font-weight: 900; line-height: 1.1; margin-bottom: 1.5rem; }
        .hero-subtitle { font-size: 1.1rem; opacity: 0.9; margin-bottom: 2rem; max-width: 600px; }
        .btn-cta {
            background: var(--secondary); color: var(--text-dark); padding: 0.85rem 1.75rem; border-radius: 8px;
            font-weight: 700; border: none; text-decoration: none; display: inline-block; transition: all 0.3s;
        }
        .btn-cta:hover { background: #e0a800; transform: translateY(-2px); color: var(--text-dark); }

        .stats-section { padding: 5rem 0; background: white; }
        .stat-card-modern {
            background: white; border-radius: 12px; padding: 2rem 1.5rem; text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06); transition: all 0.3s; height: 100%;
        }
        .stat-card-modern:hover { transform: translateY(-5px); box-shadow: 0 8px 30px rgba(0,0,0,0.12); }
        .stat-icon { width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 2rem; }
        .stat-number { font-size: 2.5rem; font-weight: 900; color: var(--text-dark); line-height: 1; margin-bottom: 0.75rem; }
        .stat-label { font-family: 'JetBrains Mono', monospace; font-size: 0.7rem; font-weight: 600; color: var(--text-muted); letter-spacing: 1.5px; text-transform: uppercase; }

        .section-padding { padding: 5rem 0; }
        .section-label { font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; font-weight: 600; color: var(--primary); letter-spacing: 2px; text-transform: uppercase; margin-bottom: 0.75rem; }
        .section-title { font-size: 2rem; font-weight: 800; color: var(--text-dark); margin-bottom: 1rem; }
        .card-custom { background: white; border-radius: 12px; border: 1px solid var(--border); box-shadow: 0 2px 8px rgba(0,0,0,0.04); }

        .tutorial-card {
            background: white; border-radius: 12px; border: 1px solid var(--border);
            box-shadow: 0 4px 20px rgba(0,0,0,0.06); transition: all 0.3s;
        }
        .tutorial-card:hover { transform: translateY(-5px); box-shadow: 0 8px 30px rgba(0,0,0,0.12); }
        .tutorial-number {
            width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            margin: 0 auto; font-size: 2rem; font-weight: 900; box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .footer-custom { background: var(--bg-light); padding: 4rem 0 2rem; border-top: 1px solid var(--border); }
        .footer-title { font-weight: 800; color: var(--primary); font-size: 1.5rem; margin-bottom: 1rem; }
        .footer-link { color: var(--text-muted); text-decoration: none; display: inline-block; margin-bottom: 0.5rem; font-size: 0.9rem; transition: color 0.2s; }
        .footer-link:hover { color: var(--primary); }
        .footer-label { font-family: 'JetBrains Mono', monospace; font-size: 0.7rem; font-weight: 600; letter-spacing: 2px; color: var(--text-dark); margin-bottom: 1rem; text-transform: uppercase; }

        .btn-primary-custom { background: var(--primary); color: white; padding: 0.85rem 1.75rem; border-radius: 8px; font-weight: 600; border: none; text-decoration: none; display: inline-block; }
        .btn-primary-custom:hover { background: var(--primary-light); color: white; }

        @media (max-width: 768px) {
            .hero-title { font-size: 2rem; }
            .nav-menu-center { flex-direction: column; gap: 1rem; align-items: flex-start; padding-left: 1rem; }
            .nav-actions { margin-top: 1rem; width: 100%; justify-content: center; }
            .stat-number { font-size: 2rem; }
            .text-lg-end { text-align: left !important; }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand navbar-brand-custom" href="<?php echo e(route('landing.index')); ?>">GuruKuu</a>
            <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navMenu"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navMenu">
                <div class="nav-menu-center">
                    <a class="nav-link nav-link-custom" href="#home">Beranda</a>
                    <a class="nav-link nav-link-custom" href="#guru">Guru</a>
                    <a class="nav-link nav-link-custom" href="#panduan">Panduan</a>
                    <a class="nav-link nav-link-custom" href="#tentang">Tentang</a>
                </div>
                                <div class="nav-actions">
                 <?php if(auth()->guard()->check()): ?>
    <?php
        $userRole = Auth::user()->role;
        $dashboardUrl = match($userRole) {
            'admin' => route('admin.dashboard'),
            'guru' => route('guru.dashboard'),
            'siswa' => route('siswa.dashboard'),
            default => route('landing.index')
        };
        $dashboardLabel = match($userRole) {
            'admin' => 'Dashboard Admin',
            'guru' => 'Dashboard Guru',
            'siswa' => 'Dashboard Siswa',
            default => 'Dashboard'
        };
    ?>
    
    <a href="<?php echo e($dashboardUrl); ?>" class="btn btn-dashboard">
        <i class="bi bi-speedometer2 me-1"></i> <?php echo e($dashboardLabel); ?>

    </a>
    
    <form action="<?php echo e(route('logout')); ?>" method="POST" class="d-inline">
        <?php echo csrf_field(); ?>
        <button type="submit" class="btn btn-danger btn-sm">
            <i class="bi bi-box-arrow-right me-1"></i> Keluar
        </button>
    </form>
<?php else: ?>
    <a href="<?php echo e(route('login')); ?>" class="btn btn-primary">
        <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
    </a>
<?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <?php echo $__env->yieldContent('content'); ?>

    <footer class="footer-custom">
        <div class="container">
            <div class="row align-items-start">
                <div class="col-lg-7 mb-4">
                    <div class="footer-title">GuruKuu</div>
                    <p class="text-muted mb-0" style="font-size: 0.9rem; max-width: 500px;">
                        Sistem Manajemen Penilaian Guru Berbasis Siswa untuk SMK unggulan di seluruh Indonesia.
                    </p>
                </div>
                <div class="col-lg-5 mb-4 text-lg-end">
                    <div class="footer-label">LEGAL & BANTUAN</div>
                    <a href="<?php echo e(route('legal.privacy')); ?>" class="footer-link me-3">Kebijakan Privasi</a>
                    <a href="<?php echo e(route('legal.terms')); ?>" class="footer-link me-3">Syarat & Ketentuan</a>
                    <a href="<?php echo e(route('kontak.guest.page')); ?>" class="footer-link">Hubungi Admin</a>
                </div>
            </div>
            <hr class="border-secondary my-4">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="text-muted mb-0" style="font-size: 0.85rem;">&copy; <?php echo e(date('Y')); ?> GuruKuu. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    
    <script>
        AOS.init({ duration: 800, once: true });

        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.nav-link-custom');
            const navbar = document.querySelector('.navbar-custom');
            const navbarHeight = navbar ? navbar.offsetHeight : 80;
            
            const guruSection = document.getElementById('guru');
            const panduanSection = document.getElementById('panduan');
            const tentangSection = document.getElementById('tentang');
            
            const isLandingPage = guruSection !== null;

            function updateActiveNav() {
                if (!isLandingPage) {
                    navLinks.forEach(link => {
                        link.classList.remove('active');
                        if (link.getAttribute('href') === '#tentang') link.classList.add('active');
                    });
                    return;
                }

                const scrollPos = window.scrollY + navbarHeight + 150;
                let activeId = 'home';

                if (tentangSection && scrollPos >= tentangSection.offsetTop) {
                    activeId = 'tentang';
                } else if (panduanSection && scrollPos >= panduanSection.offsetTop) {
                    activeId = 'panduan';
                } else if (guruSection && scrollPos >= guruSection.offsetTop) {
                    activeId = 'guru';
                }

                navLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === '#' + activeId) link.classList.add('active');
                });
            }

            let ticking = false;
            window.addEventListener('scroll', function() {
                if (!ticking) {
                    window.requestAnimationFrame(function() { updateActiveNav(); ticking = false; });
                    ticking = true;
                }
            });
            updateActiveNav();

            if (isLandingPage) {
                document.querySelectorAll('a[href^="#"]').forEach(link => {
                    link.addEventListener('click', function(e) {
                        const href = this.getAttribute('href');
                        if (href.startsWith('#') && href.length > 1) {
                            const target = document.getElementById(href.substring(1));
                            if (target) {
                                e.preventDefault();
                                window.scrollTo({ top: target.offsetTop - navbarHeight, behavior: 'smooth' });
                                const navCollapse = document.getElementById('navMenu');
                                if (navCollapse && navCollapse.classList.contains('show')) new bootstrap.Collapse(navCollapse).hide();
                            }
                        }
                    });
                });
            }
        });
    </script>
</body>
</html><?php /**PATH C:\Users\ADVAN\PROJEK\Laravel\GuruKuu\resources\views/layouts/landing.blade.php ENDPATH**/ ?>