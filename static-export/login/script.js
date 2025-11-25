/**
 * Login Page JavaScript
 */

// Password Toggle Functionality
const togglePassword = document.getElementById('togglePassword');
const passwordInput = document.getElementById('password');
const eyeIcon = document.getElementById('eyeIcon');

togglePassword.addEventListener('click', function() {
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);

    // Toggle icon
    if (type === 'text') {
        eyeIcon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
        `;
    } else {
        eyeIcon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        `;
    }
});

// Form Submit Handler
const loginForm = document.getElementById('loginForm');

loginForm.addEventListener('submit', async function(e) {
    e.preventDefault();

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    try {
        showLoading(true);

        // Simulated API call (replace with actual API endpoint)
        const response = await fetch('/api/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ email, password })
        });

        const data = await response.json();

        showLoading(false);

        if (data.success) {
            // Redirect based on user role
            if (data.role === 'yonetici') {
                window.location.href = '../yonetici/dashboard/';
            } else {
                window.location.href = '../personel/dashboard/';
            }
        } else {
            showFlashMessage(data.message || 'Giriş başarısız', 'error');
        }

    } catch (error) {
        showLoading(false);
        showFlashMessage('Bir hata oluştu. Lütfen tekrar deneyin.', 'error');
        console.error('Login error:', error);
    }
});

/**
 * Show flash message
 */
function showFlashMessage(message, type = 'error') {
    const container = document.getElementById('flashMessages');

    const colorClasses = {
        error: 'bg-red-50 border-red-500 text-red-800',
        success: 'bg-green-50 border-green-500 text-green-800',
        warning: 'bg-yellow-50 border-yellow-500 text-yellow-800'
    };

    const iconPaths = {
        error: '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>',
        success: '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>'
    };

    const classes = colorClasses[type] || colorClasses.error;
    const icon = iconPaths[type] || iconPaths.error;

    const flashHTML = `
        <div class="mb-6 border-l-4 ${classes} rounded-lg p-4 fade-in">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    ${icon}
                </svg>
                <p class="text-sm font-medium">${message}</p>
            </div>
        </div>
    `;

    container.innerHTML = flashHTML;

    // Auto dismiss after 5 seconds
    setTimeout(() => {
        const flash = container.querySelector('div');
        if (flash) {
            flash.style.transition = 'opacity 0.5s';
            flash.style.opacity = '0';
            setTimeout(() => flash.remove(), 500);
        }
    }, 5000);
}

// Demo: Show sample credentials (remove in production)
console.log('='.repeat(50));
console.log('DEMO MODE - Sample Credentials:');
console.log('Yönetici: yonetici@dogu.com / admin123');
console.log('Personel: personel@dogu.com / personel123');
console.log('='.repeat(50));
