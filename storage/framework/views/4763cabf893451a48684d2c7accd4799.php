
<?php $__env->startSection('title', 'Login'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex align-items-center justify-content-center" style="min-height: 100vh; padding: 40px 0;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-4 col-lg-5 col-md-7">
                <div class="card-custom p-4 p-md-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-mortarboard-fill" style="font-size: 3rem; color: var(--primary);"></i>
                        <h3 class="fw-bold mt-3" style="color: var(--primary); font-size: 1.5rem;">Masuk ke GuruKuu</h3>
                        <p class="text-muted mb-0" style="font-size: 0.9rem;">Pilih peran dan masukkan kredensial Anda</p>
                    </div>

                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger border-0 mb-3" style="background: #fee2e2; color: #991b1b;">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="small"><i class="bi bi-exclamation-triangle"></i> <?php echo e($error); ?></div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo e(route('login.post')); ?>" method="POST" id="loginForm">
                        <?php echo csrf_field(); ?>
                        
                        <!-- PILIHAN PERAN LOGIN -->
                        <div class="mb-3">
                            <label class="form-label font-mono" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 1px;">PERAN LOGIN</label>
                            <div class="d-flex gap-2">
                                <input type="radio" class="btn-check" name="login_role" id="roleSiswa" value="siswa" checked onchange="updateLoginForm()">
                                <label class="btn btn-outline-primary flex-fill py-2" for="roleSiswa">
                                    <i class="bi bi-person-fill me-1"></i> Siswa
                                </label>

                                <input type="radio" class="btn-check" name="login_role" id="roleGuru" value="guru" onchange="updateLoginForm()">
                                <label class="btn btn-outline-primary flex-fill py-2" for="roleGuru">
                                    <i class="bi bi-chalkboard-teacher me-1"></i> Guru
                                </label>

                                <input type="radio" class="btn-check" name="login_role" id="roleAdmin" value="admin" onchange="updateLoginForm()">
                                <label class="btn btn-outline-primary flex-fill py-2" for="roleAdmin">
                                    <i class="bi bi-shield-lock-fill me-1"></i> Admin
                                </label>
                            </div>
                        </div>

                        <!-- NIS (Label sudah diubah, tanpa "NIY") -->
                        <div class="mb-3">
                            <label class="form-label font-mono" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 1px;">NIS</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-person-badge text-muted"></i></span>
                                <input type="text" name="nis" id="nisInput" class="form-control border-start-0 ps-0" 
                                    value="<?php echo e(old('nis')); ?>" placeholder="Masukkan NIS" required style="border-radius: 0 8px 8px 0; padding: 0.65rem 1rem;">
                            </div>
                        </div>

                        <!-- CONTAINER TANGGAL LAHIR (Hanya untuk Siswa) -->
                        <div class="mb-3" id="containerTanggalLahir">
                            <label class="form-label font-mono" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 1px;">TANGGAL LAHIR</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-calendar-event text-muted"></i></span>
                                <input type="date" name="tanggal_lahir" id="dobInput" class="form-control border-start-0 ps-0" 
                                    value="<?php echo e(old('tanggal_lahir')); ?>" style="border-radius: 0 8px 8px 0; padding: 0.65rem 1rem;">
                            </div>
                        </div>

                        <!-- CONTAINER PASSWORD (Untuk Guru & Admin) -->
                        <div class="mb-4" id="containerPassword" style="display: none;">
                            <label class="form-label font-mono" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 1px;">PASSWORD</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-lock text-muted"></i></span>
                                <input type="password" name="password" id="passwordInput" class="form-control border-start-0 border-end-0 ps-0" 
                                    placeholder="Masukkan Password" style="border-radius: 0; padding: 0.65rem 1rem;">
                                <button type="button" class="input-group-text bg-white border-start-0" id="togglePassword" style="border-radius: 0 8px 8px 0; cursor: pointer; border-left: none;">
                                    <i class="bi bi-eye text-muted" id="toggleIcon"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" id="submitBtn" class="btn btn-masuk w-100 py-2" style="font-size: 1rem;">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Lanjutkan
                        </button>
                    </form>

                    <div class="text-center mt-4 pt-3 border-top">
                        <small class="text-muted d-block mb-2">Ada masalah dengan login?</small>
                        <a href="<?php echo e(route('kontak.guest.page')); ?>" class="text-decoration-none" style="color: var(--primary); font-size: 0.85rem;">
                            <i class="bi bi-chat-dots me-1"></i> Hubungi Admin
                        </a>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="<?php echo e(route('landing.index')); ?>" class="text-decoration-none text-muted fw-medium" style="font-size: 0.9rem;">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateLoginForm() {
    const roleSiswa = document.getElementById('roleSiswa');
    const roleGuru = document.getElementById('roleGuru');
    const roleAdmin = document.getElementById('roleAdmin');
    const containerTanggalLahir = document.getElementById('containerTanggalLahir');
    const containerPassword = document.getElementById('containerPassword');
    const dobInput = document.getElementById('dobInput');
    const passwordInput = document.getElementById('passwordInput');
    const submitBtn = document.getElementById('submitBtn');
    
    let selectedRole = 'siswa';
    
    if (roleGuru.checked) {
        selectedRole = 'guru';
    } else if (roleAdmin.checked) {
        selectedRole = 'admin';
    }
    
    if (selectedRole === 'guru' || selectedRole === 'admin') {
        containerTanggalLahir.style.display = 'none';
        dobInput.removeAttribute('required');
        
        containerPassword.style.display = 'block';
        passwordInput.setAttribute('required', 'required');
        
        if (selectedRole === 'admin') {
            submitBtn.innerHTML = '<i class="bi bi-shield-lock-fill me-2"></i> Masuk sebagai Admin';
        } else {
            submitBtn.innerHTML = '<i class="bi bi-chalkboard-teacher me-2"></i> Masuk sebagai Guru';
        }
        
        setTimeout(function() {
            passwordInput.focus();
        }, 300);
        
    } else {
        containerTanggalLahir.style.display = 'block';
        dobInput.setAttribute('required', 'required');
        
        containerPassword.style.display = 'none';
        passwordInput.removeAttribute('required');
        submitBtn.innerHTML = '<i class="bi bi-box-arrow-in-right me-2"></i> Lanjutkan';
    }
}

document.getElementById('togglePassword').addEventListener('click', function(e) {
    e.preventDefault();
    const passwordInput = document.getElementById('passwordInput');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('bi-eye');
        toggleIcon.classList.add('bi-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('bi-eye-slash');
        toggleIcon.classList.add('bi-eye');
    }
});

document.addEventListener('DOMContentLoaded', function() {
    updateLoginForm();
});

document.getElementById('loginForm').addEventListener('submit', function(e) {
    const roleGuru = document.getElementById('roleGuru');
    const roleAdmin = document.getElementById('roleAdmin');
    const passwordInput = document.getElementById('passwordInput');
    const dobInput = document.getElementById('dobInput');
    
    if (roleGuru.checked || roleAdmin.checked) {
        if (!passwordInput.value) {
            e.preventDefault();
            passwordInput.classList.add('is-invalid');
            passwordInput.focus();
        }
    } else {
        if (!dobInput.value) {
            e.preventDefault();
            dobInput.classList.add('is-invalid');
            dobInput.focus();
        }
    }
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADVAN\PROJEK\Laravel\GuruKuu\resources\views/auth/login.blade.php ENDPATH**/ ?>