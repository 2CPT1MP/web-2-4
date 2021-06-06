import {postJSON} from './fetchJson.js';
const newPost = {};

const loginElement = document.getElementById("login");
loginElement.addEventListener("blur", async () => {
    newPost.login = loginElement.value;
    const response = await postJSON(`/check-credentials`, newPost);
    loginElement.style.backgroundColor = response.uniqueLogin? "rgba(0, 255, 0, .25)" : "rgba(255, 0, 0, .25)";
    loginElement.style.border = "none";

});

const emailElement = document.getElementById("email");
emailElement.addEventListener("blur", async () => {
    newPost.email = emailElement.value;
    const response = await postJSON(`/check-credentials`, newPost);
    emailElement.style.backgroundColor = response.uniqueEmail? "rgba(0, 255, 0, .25)" : "rgba(255, 0, 0, .25)";
    emailElement.style.border = "none";
});



