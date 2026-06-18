// 1. Connect to our local Node.js WebSocket Server
const socket = io('http://localhost:3000');

// 2. Initialize Bootstrap Toast component instance
const toastElement = document.getElementById('liveToast');
const toastMessage = document.getElementById('toastMessage');
const bootstrapToast = new bootstrap.Toast(toastElement, { delay: 5000 });

// 3. Listen for the 'live-activity' broadcast event
socket.on('live-activity', (data) => {
    console.log("Real-time payload received:", data);

    // Inject text and trigger the slide notification
    toastMessage.innerHTML = `New Book Added: <strong>"${data.title}"</strong> by ${data.author} at ${data.time}!`;
    bootstrapToast.show();
});