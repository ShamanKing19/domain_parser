const App = require('./parser/app.js');

const app = new App();
const args = process.argv.slice(2);

if(args.length === 0) {
    app.run();
} else {
    app.runWithParams(args);
}
