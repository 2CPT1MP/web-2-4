const fetchJSON = (method, url, body) => {
    return new Promise(async (resolve, reject) => {
        const options = {
            method,
            headers: { 'Content-Type': 'application/json' },
            body
        };
        const response = await fetch(url, options);
        if (response.ok)
            return resolve(await response.json());
        return reject("Request failed");
    });
}

export const getJSON = (url) => {
    return fetchJSON('GET', url, null);
}

export const postJSON = (url, body) => {
    return fetchJSON('POST', url, JSON.stringify(body));
}

