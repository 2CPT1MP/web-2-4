import {getJSON, postJSON} from './fetchJson.js';

getJSON('https://jsonplaceholder.typicode.com/todos/1')
    .then(r => console.log(r));


const newPost = {
    title: "foo",
    body: 'bar',
    userId: 1
};

postJSON('https://jsonplaceholder.typicode.com/posts', newPost)
    .then(r => console.log(r));

