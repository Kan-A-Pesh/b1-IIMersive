const CONFIG = require('./config.json');
const express = require('express');
const http = require('http');

const app = express();
const server = http.createServer(app);
const io = require('socket.io')(server, {
    cors: {
        origin: "*",
        methods: ["GET", "POST"]
    }
});

const port = CONFIG.PORT;

// Prevent CORS errors
app.use((req, res, next) => {
    res.header('Access-Control-Allow-Origin', '*');
    res.header('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');
    next();
});

app.get('/message/:handle', (req, res) => {
    console.log("SEND: WP-MSG " + req.params.handle);
    io.to(req.params.handle).emit('WP-MSG', 'Wake up!');
    res.send("Sending wake-up message to " + req.params.handle);
});

io.on('connection', (socket) => {
    console.log(`SYSM: ${socket.id} connected`);
    
    socket.on('AUTH', (handle) => {
        console.log(`AUTH: ${socket.id} authenticated as ${handle}`);
        socket.join(handle);
    });

    socket.on('disconnect', () => {
        console.log(`SYSM: ${socket.id} disconnected`);
    });
});

server.listen(port, () => {
    console.log(`Listening on port ${port}`);
});