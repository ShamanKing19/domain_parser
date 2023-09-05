const App = require('./parser/app.js');

const app = new App();
(async() => {
    await app.run();
});
