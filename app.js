const express = require('express');
const { createProxyMiddleware } = require('http-proxy-middleware');

const app = express();
const PORT = process.env.PORT || 3000;

app.use('/', createProxyMiddleware({
    target: 'http://localhost:80',
    changeOrigin: true,
}));

app.use((req, res, next) => {
    if (req.path !== '/' && req.path.slice(-4) === '.php') {
        const newPath = req.path.slice(0, -4);
        req.url = newPath;
    }
    next();
});

app.listen(PORT, () => {
    console.log(`Server is running on http://localhost:${PORT}`);
});
