if (document.cookie.includes('session_id'))
{
    window.location.href = '/';
}

const errorElement = document.querySelector('#error');

const handleInput = document.querySelector('#username');
const passwordInput = document.querySelector('#password');
const rememberMeInput = document.querySelector('#rememberMe');

const loginButton = document.querySelector('#submit');

const toggleDisabled = (disabledStatus) => {
    handleInput.disabled = disabledStatus;
    passwordInput.disabled = disabledStatus;
    rememberMeInput.disabled = disabledStatus;
    loginButton.disabled = disabledStatus;

    loginButton.innerText = disabledStatus ? 'Logging in...' : 'Login';
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

const login = async (event) => {

    const handle = handleInput.value;
    const password = passwordInput.value;
    const rememberMe = rememberMeInput.checked;

    passwordInput.value = '';

    toggleDisabled(true);

    try {
        const response = await POST('/auth', {
            handle: handle,
            password: password,
            remember_me: rememberMe
        });

        const payload = response.payload;

        const expirationDate = parseDate(payload.expires);
        document.cookie = `session_id=${payload.session_id}; path=/; SameSite=Strict; Secure; expires=${expirationDate.toUTCString()}`;

        localStorage.setItem('remember_me', rememberMe);

        if (rememberMe)
        {
            localStorage.setItem('user_handle', payload.user_handle);
        }
        else 
        {
            sessionStorage.setItem('user_handle', payload.user_handle);
        }

        window.location.href = '/';
    }
    catch (error) {
        toggleDisabled(false);
        logError(error.message);
        return;
    }
}

const update = () => {
    loginButton.disabled = handleInput.value === '' || passwordInput.value === '';
    logError(null);
}

loginButton.addEventListener('click', login);

handleInput.addEventListener('input', update);
passwordInput.addEventListener('input', update);

if (USER_HANDLE !== null)
{
    handleInput.value = USER_HANDLE;
    passwordInput.focus();
}
else
{
    handleInput.focus();
}

update();