if (document.cookie.includes('session_id'))
{
    window.location.href = '/';
}

const errorElement = document.querySelector('#error');

const emailInput = document.querySelector('#email');
const handleInput = document.querySelector('#username');
const passwordInput = document.querySelector('#password');
const repasswordInput = document.querySelector('#repassword');

const registerButton = document.querySelector('#submit');

const toggleDisabled = (disabledStatus) => {
    emailInput.disabled = disabledStatus;
    handleInput.disabled = disabledStatus;
    passwordInput.disabled = disabledStatus;
    repasswordInput.disabled = disabledStatus;
    registerButton.disabled = disabledStatus;

    registerButton.innerText = disabledStatus ? 'Loading...' : 'Register';
}

const logError = (errorText) => {
    if (errorText === null)
    {
        errorElement.classList.remove('mt-3');
        errorElement.innerText = '';
        return;
    }


    errorElement.classList.add('mt-3');
    errorElement.innerText = errorText;
}

const register = async () => {

    const email = emailInput.value;
    const handle = handleInput.value;
    const password = passwordInput.value;
    const repassword = repasswordInput.value;

    passwordInput.value = '';
    repasswordInput.value = '';

    if (password !== repassword)
    {
        logError('Passwords do not match');
        return;
    }

    if (RegExp(/^[a-zA-Z0-9_]{1,15}$/).test(handle) === false)
    {
        logError('Invalid username (must be 1-15 characters long and only contain letters, numbers, and underscores)');
        return;
    }

    if (password.length < 8)
    {
        logError('Password must be at least 8 characters long');
        return;
    }

    toggleDisabled(true);

    try {
        const response = await POST('/users', {
            handle: handle,
            email: email,
            password: password
        });

        const payload = response.payload;
        sessionStorage.setItem('user_handle', payload.user_handle);

        // TODO: Redirect to /confirm and send email
        window.location.href = '/confirm/null';
    }
    catch (error) {
        toggleDisabled(false);
        logError(error.message);
        return;
    }
}

const update = () => {
    registerButton.disabled =
        emailInput.value === '' ||
        handleInput.value === '' ||
        passwordInput.value === '' ||
        repasswordInput.value === '';

    logError(null);
}

registerButton.addEventListener('click', register);

emailInput.addEventListener('input', update);
handleInput.addEventListener('input', update);
passwordInput.addEventListener('input', update);
repasswordInput.addEventListener('input', update);

emailInput.focus();
update();