
const enableEditHandler = (event) => {
    event.target.parentNode.setAttribute('hidden', '');
    const id = event.target.parentNode.id;
    const editBlock = document.getElementById(`form-${id}`);
    editBlock.removeAttribute('hidden');
}

const submitMsgHandler = (event) => {
    event.preventDefault();

    const topic = event.target.elements.topic.value;
    const text = event.target.elements.text.value;
    const postId = event.target.elements.postId.value;

    const editBlock = document.getElementById(`form-${postId}`);
    editBlock.removeAttribute('hidden');

    const editedPost = {
        topic,
        text
    }

    const req = $.ajax(`/blog/edit-comment?postId=${postId}`, {
        contentType: 'application/json',
        data: JSON.stringify(editedPost),
        method: 'POST',
        success: (msg) => {
            event.target.setAttribute('hidden', 'true');
            const messageBlock = document.getElementById(postId);
            messageBlock.removeAttribute('hidden');

            const textNode = document.getElementById(`text-${postId}`);
            textNode.innerText = msg.text;
            const topicNode = document.getElementById(`topic-${postId}`);
            topicNode.innerText = msg.topic;
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const buttons = document.querySelectorAll('.msg-block > button');
    const submitForms = document.querySelectorAll(".msg-block-form");

    for (const button of buttons)
        button.addEventListener('click', enableEditHandler);

    for (const form of submitForms)
        form.addEventListener('submit', submitMsgHandler);
});