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

    console.log(xmlDoc);

    postXML("https://localhost3434.ri", xmlDoc).then((r) => {
        console.log(r)
    }).catch(r => {
        const parentContainer = document.getElementById(postId);
        const newComment = document.createElement("div");
        newComment.innerHTML = message;
        parentContainer.appendChild(newComment)
    });
}

for (let messageForm of messageForms)
    messageForm.addEventListener('submit', submitHandler);