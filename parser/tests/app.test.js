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
    expect(domains[0]).toHaveProperty('id');
    expect(domains[0]).toHaveProperty('domain');

    const domains2 = await app.getDomains(1, 12);
    expect(domains2.length).toBe(12);
});

test('get last page number', async () => {
    const page = await app.getLastPageNumber(1);
    expect(page).toBeGreaterThanOrEqual(1);
});

test('get domains count', async () => {
    const count = await app.getDomainsCount();
    expect(count).toBeGreaterThanOrEqual(1);
});
