const API_BASE_URL = '/api';
const HEADERS = {
    'Content-Type': 'application/json'
};

const USER_HANDLE =
    localStorage.getItem('userHandle') ||
    sessionStorage.getItem('userHandle') ||
    null;

const parseResponse = async (response) => {
    const json = await response.json();

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