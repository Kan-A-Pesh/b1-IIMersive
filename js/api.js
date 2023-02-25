const API_BASE_URL = '/api';
const HEADERS = {
    'Content-Type': 'application/json'
};

const USER_HANDLE =
    localStorage.getItem('user_handle') ||
    sessionStorage.getItem('user_handle') ||
    null;

const parseResponse = async (response) => {
    if (response.status === 401) {
        document.cookie = "session_id=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;"
        window.location.href = "/login";
    }

    const text = await response.text();
    if (!text.startsWith('{'))
    {
        console.log(text);
        throw text;
    }

    const json = JSON.parse(text);
    //const json = await response.json();

    if (json.success === false)
        throw json;

    return json;
};


const GET = async (url, params) => {
    const response = await fetch(
        API_BASE_URL + url +
        '?' + new URLSearchParams(params),
        {
            method: 'GET',
            headers: HEADERS,
            redirect: 'manual'
        }
    );

    return await parseResponse(response);
};

const POST = async (url, body) => {
    const response = await fetch(
        API_BASE_URL + url,
        {
            method: 'POST',
            headers: HEADERS,
            redirect: 'manual',
            body: JSON.stringify(body)
        }
    );
    
    return await parseResponse(response);
};

const PUT = async (url, body) => {
    const response = await fetch(
        API_BASE_URL + url,
        {
            method: 'PUT',
            headers: HEADERS,
            redirect: 'manual',
            body: JSON.stringify(body)
        }
    );
    
    return await parseResponse(response);
};

const DELETE = async (url, body) => {
    const response = await fetch(
        API_BASE_URL + url,
        {
            method: 'DELETE',
            headers: HEADERS,
            redirect: 'manual',
            body: JSON.stringify(body)
        }
    );
    
    return await parseResponse(response);
};

const imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
const videoExtensions = ['mp4', 'webm', 'ogg'];
const audioExtensions = ['mp3', 'wav'];

const parseMedia = (media, defaultMedia) => {
    if (!media)
        return defaultMedia;

    const id = media.substring(0, 16);
    const ext = media.substring(16, 20).trim();
    
    if (imageExtensions.includes(ext))
    {
        return `<img src="/media/${id}.${ext}" />`;
    }
    else if (videoExtensions.includes(ext))
    {
        return `<video src="/media/${id}.${ext}" controls />`;
    }
    else if (audioExtensions.includes(ext))
    {
        return `<audio src="/media/${id}.${ext}" controls />`;
    }

    return "/media/" + id + "." + ext;
};

const parseDate = (apiDate) => {
    const {date, timezone_type, timezone} = apiDate;
    if (timezone !== 'UTC') throw 'Invalid timezone';

    const localTimezone = new Date().getTimezoneOffset() / -60;
    const localDate = new Date(date);

    localDate.setHours(localDate.getHours() + localTimezone);

    return localDate;
};

const toRelativeTime = (date) => {
    const seconds = Math.floor((new Date() - date) / 1000);

    if (seconds < 15)
        return 'maintenant';

    if (seconds < 60)
        return seconds + 's';
    
    minutes = Math.floor(seconds / 60);
    if (minutes < 60)
        return minutes + 'm';

    hours = Math.floor(minutes / 60);
    if (hours < 24)
        return hours + 'h';

    days = Math.floor(hours / 24);
    if (days < 7)
        return days + 'j';

    weeks = Math.floor(days / 7);
    if (weeks < 52)
        return weeks + 'sem.';

    years = Math.floor(weeks / 52);
    if (years < 10)
        return years + 'ans';

    return 'longtemps! (oups)';
}

const checkSession = async () => {
    if (!document.cookie.includes('session_id'))
        return;

    try {
        const remember_me = localStorage.getItem('remember_me') === 'true';
        const request = await PUT('/auth', {remember_me: remember_me});
        const expires = request.payload.expires;
        const date = parseDate(expires);
        
        const session_id = document.cookie.split('; ').find(row => row.startsWith('session_id')).split('=')[1];
        document.cookie = `session_id=${session_id}; expires=${date.toUTCString()}; path=/; SameSite=Strict; Secure`;

    } catch (error) {
        console.log(error)
    }
}

// Check session every minute
setTimeout(checkSession, 60 * 1000);