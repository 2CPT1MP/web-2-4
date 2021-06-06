import {postXML} from './xhrXml.js';

const messageForms = document.getElementsByClassName("message-form");
const submitHandler = (event) => {
    event.preventDefault();
    const message = event.target.elements.message.value;
    const postId = event.target.elements.postId.value;

    const xmlDoc = document.implementation.createDocument(null, "comment");
    const messageNode = xmlDoc.createElement("message");
    const messageTextNode = xmlDoc.createTextNode(message);

    const postIdNode = xmlDoc.createElement("postId");
    const postIdTextNode = xmlDoc.createTextNode(postId);

    messageNode.appendChild(messageTextNode);
    postIdNode.appendChild(postIdTextNode);
    xmlDoc.children[0].appendChild(messageNode);
    xmlDoc.children[0].appendChild(postIdNode);


    postXML(`/blog/add-comment`, xmlDoc).then((r) => {
        const parser = new DOMParser();
        const xmlDoc = parser.parseFromString(r,"text/xml");

        const message = xmlDoc.getElementsByTagName("message")[0].textContent;
        const timestamp = xmlDoc.getElementsByTagName("timestamp")[0].textContent;
        const name = xmlDoc.getElementsByTagName("name")[0].textContent;

        const parentContainer = document.getElementById("messages-container-" + postId);
        const newComment = document.createElement("div");
        newComment.innerHTML = `${timestamp} <b>${name}</b> ${message}`;
        parentContainer.prepend(newComment);
    }).catch(r => {
        console.log("ERROR");
    });
}

for (let messageForm of messageForms)
    messageForm.addEventListener('submit', submitHandler);