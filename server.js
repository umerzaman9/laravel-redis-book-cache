const express = require('express');
const app = express();
const http = require('http').createServer(app);
const io = require('socket.io')(http, {
    cors: { origin: "*" } // Allows our Laravel app to connect securely
});
const { createClient } = require('redis');

// 1. Configuration of Cloud Redis Credentials
const redisUrl = `redis://default:17l40KnJbdZW8QnzBig0KGgVfsqqh1z6@land-sound-plane-57685.db.redis.io:17317`;
const subscriber = createClient({ url: redisUrl });

async function startServer() {
    // 2. Connect to Redis Cloud Instance
    await subscriber.connect();
    console.log('Connected to Redis Cloud for Pub/Sub Subscriptions.');

    // 3. Subscribe to the "book-actions" channel
    await subscriber.subscribe('book-actions', (message) => {
        console.log(`Received Message from Redis Pub/Sub: ${message}`);

        // 4. Instantly broadcast the message to all connected web browsers
        io.emit('live-activity', JSON.parse(message));
    });

    // Start WebSocket Server on Port 3000
    http.listen(3000, () => {
        console.log('WebSocket Real-Time Bridge running on http://localhost:3000');
    });
}

startServer().catch(err => console.error('Redis Connection Error:', err));