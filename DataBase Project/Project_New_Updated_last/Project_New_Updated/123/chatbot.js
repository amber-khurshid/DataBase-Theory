function sendMessage() {
    const userInput = document.getElementById('userInput').value;
    if (userInput.trim() === '') return;  // Avoid sending empty messages
    fetch('chatbot.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message: userInput })
    })
    .then(response => response.json())
    .then(data => {
        const messagesDiv = document.getElementById('messages');
        messagesDiv.innerHTML += `<p><strong>You:</strong> ${userInput}</p>`;
        messagesDiv.innerHTML += `<p><strong>Bot:</strong> ${data.response}</p>`;
        document.getElementById('userInput').value = '';  // Clear input field
    });
}

