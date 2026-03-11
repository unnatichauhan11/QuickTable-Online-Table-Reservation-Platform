// Validation Functions

// Form field validation
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validatePhoneNumber(phone) {
    const re = /^[0-9]{10,15}$/;
    return re.test(phone);
}

function validatePassword(password) {
    return password.length >= 6;
}

// Registration form validation
const registerForm = document.getElementById('registerForm');
if (registerForm) {
    registerForm.addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const email = document.getElementById('email').value;
        const contact = document.getElementById('contact').value;

        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Passwords do not match!');
            return false;
        }

        if (!validatePassword(password)) {
            e.preventDefault();
            alert('Password must be at least 6 characters!');
            return false;
        }

        if (!validateEmail(email)) {
            e.preventDefault();
            alert('Invalid email format!');
            return false;
        }

        if (!validatePhoneNumber(contact)) {
            e.preventDefault();
            alert('Invalid phone number! Must be 10-15 digits.');
            return false;
        }
    });
}

// Login form validation
const loginForm = document.getElementById('loginForm');
if (loginForm) {
    loginForm.addEventListener('submit', function(e) {
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        if (!email || !password) {
            e.preventDefault();
            alert('All fields are required!');
            return false;
        }

        if (!validateEmail(email)) {
            e.preventDefault();
            alert('Invalid email format!');
            return false;
        }
    });
}

// Real-time form validation
const inputs = document.querySelectorAll('input, select, textarea');
inputs.forEach(input => {
    input.addEventListener('blur', function() {
        if (this.id === 'email' && this.value) {
            if (!validateEmail(this.value)) {
                this.classList.add('error');
                this.title = 'Invalid email format';
            } else {
                this.classList.remove('error');
            }
        }

        if (this.id === 'contact' && this.value) {
            if (!validatePhoneNumber(this.value)) {
                this.classList.add('error');
                this.title = 'Phone must be 10-15 digits';
            } else {
                this.classList.remove('error');
            }
        }

        if (this.id === 'password' && this.value) {
            if (!validatePassword(this.value)) {
                this.classList.add('error');
                this.title = 'Password must be at least 6 characters';
            } else {
                this.classList.remove('error');
            }
        }
    });
});

// Add error styling
const style = document.createElement('style');
style.textContent = `
    input.error,
    select.error,
    textarea.error {
        border-color: #f44336 !important;
        background-color: #ffebee;
    }
`;
document.head.appendChild(style);
