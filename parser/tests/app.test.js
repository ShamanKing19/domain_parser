const Parser = require('../modules/parser');
const Client = require('../modules/request');
const Logger = require('../modules/logger');
const Company = require('../modules/company_parser');
const Functions = require('../modules/functions');
const App = require('../app');

const client = new Client();
const app = new App();

// test('getting last page number', async () => {
//     const number = app.getLastPageNumber();
// });

test('get domains', async () => {
    const domains = await app.getDomains(1, 1);
    expect(domains.length).toBe(1);
});

test('', () => {

});

test('', () => {

});

test('', () => {

});


test('', () => {

});


