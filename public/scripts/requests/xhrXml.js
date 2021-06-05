const xhrXml = (method, url, body = null) => {
    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open(method, url);
        xhr.setRequestHeader("Content-Type", "text/xml")
        //xhr.responseType = "document";

        xhr.onload = () => {
            if (xhr.status >= 400)
                return reject(xhr.response);
            return resolve(xhr.response);
        }

        xhr.onerror = () => {
            return reject(xhr.response)
        };

        xhr.send(body);
    });
}

export const getXML = (url) => {
    return xhrXml("GET", url);
}

export const postXML = (url, body) => {
    return xhrXml("POST", url, body);
}