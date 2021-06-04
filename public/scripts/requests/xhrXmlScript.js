import {getXML, postXML} from './xhrXml.js';

getXML('https://api.weatherapi.com/v1/current.xml').then((r) => {
    console.log(r)
}).catch(r => {
    console.log("EL:", r.getElementsByTagName("message")[0].textContent)
});

const xmlDoc = document.implementation.createDocument(null, "person");

const nameNode = xmlDoc.createElement("name");
const nameTextNode = xmlDoc.createTextNode("Blade Wolfmoon");

const ageNode = xmlDoc.createElement("age");
ageNode.setAttribute("type", "int");
const ageTextNode = xmlDoc.createTextNode("20");

nameNode.appendChild(nameTextNode);
ageNode.appendChild(ageTextNode);

xmlDoc.children[0].appendChild(nameNode);
xmlDoc.children[0].appendChild(ageNode);

console.log(xmlDoc);

postXML("https://api.weatherapi.com/v1/current.xml", xmlDoc).then((r) => {
    console.log(r)
}).catch(r => {
    console.log("EL:", r.getElementsByTagName("message")[0].textContent)
});