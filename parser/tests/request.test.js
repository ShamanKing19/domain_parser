const Parser = require('../modules/parser');
const functions = require('../modules/functions');
const Client = require('../modules/request');
const Logger = require('../modules/logger');
const {AxiosResponse} = require('axios');
const { HTMLElement } = require('node-html-parser');

const urlList = [
    'https://jestjs.io/docs/getting-started',
    'https://www.npmjs.com/package/node-html-parser',
    'https://www.dev-notes.ru/articles/',
    'https://habr.com/ru/companies/trinion/articles/315538/',
    'https://www.1c-bitrix.ru/',
    'https://klgtu.ru/',
    'https://kantiana.ru/',
];

const testUrl1 = urlList[0];
const testUrl2 = urlList[1];
const testUrl3 = urlList[2];

const httpUrl1 = 'http://myshopify.com/';
const httpUrl2 = 'http://go.com/';
const httpUrl3 = 'http://nginx.org/';

const unipumpDomain = 'unipump.ru';
const unipumpUrl = 'https://' + unipumpDomain;

test('get 200 response via GET method', async () => {
    const client = new Client();
    const response = await client.get('https://google.com');
    expect(response).toBeInstanceOf(Object);
    expect(response.status).toBe(200);
    expect(response.statusText).toBe('OK');
    expect(response.data.length).toBeGreaterThan(0);

    const jsonResponse = await client.get('https://reqres.in/api/users');
    expect(jsonResponse.data).toBeInstanceOf(Object);
});

test('get 201 response via POST method', async () => {
    const client = new Client();
    const response = await client.post('https://reqres.in/api/users', {
        'name': 'morpheus',
        'job': 'leader'
    });

    expect(response).toBeInstanceOf(Object);
    expect(response.status).toBe(201);
    expect(response.statusText).toBe('Created');
    expect(response.data).toBeInstanceOf(Object);
});

test('get 404 response vie GET method', async () => {
    const client = new Client();
    const response = await client.get(unipumpUrl + '/ohuuiho232ohi1h');
    expect(response).toBeInstanceOf(Object);
    expect(response.status).toBe(404);
    expect(response.statusText).toBe('Not Found');

    const url1 = 'https://000SB.RU';
    const response1 = await client.get(url1);
    expect(response).toBeInstanceOf(Object);
    expect(response.status).toBe(404);
    expect(response.statusText).toBe('Not Found');
});

// test('handle timeout', async () => {
//     const client = new Client();
//
//     const url = 'https://germes-dent.ru/';
//     const response = await client.get(url, {
//         timeout: 100
//     });
//
//     expect(response).toBeInstanceOf(Object);
//     expect(response.status).toBe(408);
// });

test('make request to non-existing url via GET method', async () => {
    const client = new Client();
    const response = await client.get('https://klhj23h5ljk1jrhlk.zxc');
    expect(response).toBeFalsy();
});

test('parser make http request', async () => {
    const parser = new Parser(httpUrl1);
    const response = await parser.makeHttpRequest(parser.getDomain());
    expect(response).toBeInstanceOf(Object);
    expect(parser.checkSsl(response)).toBeFalsy();
});

test('parser make https request', async () => {
    const parser = new Parser(httpUrl1);
    const response = await parser.makeHttpsRequest(parser.getDomain());
    expect(response).toBeInstanceOf(Object);
    expect(parser.checkSsl(response)).toBeTruthy();
});

test('parser get real url', async () => {
    const parser = new Parser(unipumpUrl);
    const response = await parser.makeHttpsRequest(parser.getDomain());
    expect(parser.getRealUrl(response)).toBe(unipumpUrl + '/');

    const emptyResponse = {};
    expect(parser.getRealUrl(emptyResponse)).toBe('');

    const emptyRequestResponse = {request: {}};
    expect(parser.getRealUrl(emptyRequestResponse)).toBe('');

    const emptyResResponse = {request: {res: {}}};
    expect(parser.getRealUrl(emptyResResponse)).toBe('');

    const emptyRealUrlResponse = {request: {res: {url: ''}}};
    expect(parser.getRealUrl(emptyRealUrlResponse)).toBe('');

});

test('parser check https redirect', async () => {
    const parser1 = new Parser(httpUrl1);
    const response1 = await parser1.makeHttpRequest(parser1.getDomain());
    expect(parser1.checkHttpsRedirect(response1)).toBeFalsy();

    const parser2 = new Parser('http://' + unipumpDomain);
    const response2 = await parser2.makeHttpRequest(parser2.getDomain());
    expect(parser2.checkHttpsRedirect(response2)).toBeTruthy();

    const parser3 = new Parser('http://1000miglia-wheels.ru/');
    const response3 = await parser3.makeHttpRequest(parser3.getDomain());
    expect(parser3.checkHttpsRedirect(response3)).toBeTruthy();
});

test('parser check SSL', async () => {
    const parser1 = new Parser(httpUrl1);
    const response1 = await parser1.makeHttpRequest(parser1.getDomain());
    expect(parser1.checkSsl(response1)).toBeFalsy();

    const parser2 = new Parser(unipumpUrl);
    const response2 = await parser2.makeHttpRequest(parser2.getDomain());
    expect(parser2.checkSsl(response2)).toBeTruthy();
});

test('get response data', async () => {
    const parser = new Parser(unipumpUrl);
    const response = await parser.makeHttpsRequest(parser.getDomain());
    const data = parser.getResponseData(response);
    expect(data.length).toBeGreaterThan(1000);
});

test('get empty response data', async () => {
    const parser = new Parser('');
    const response = await parser.makeHttpsRequest(parser.getDomain());
    const data = parser.getResponseData(response);
    expect(data.length).toBe(0);
});

test('parser get html', async () => {
    const parser = new Parser(unipumpUrl);
    const response = await parser.makeHttpsRequest(parser.getDomain());
    const data = parser.getResponseData(response);
    expect(parser.getHtml(data)).toBeInstanceOf(HTMLElement);
});

test('check if there is catalog on "Bitrix" website', async () => {
    const parser1 = new Parser(unipumpUrl);
    const hasCatalog1 = await parser1.hasCatalog();
    expect(hasCatalog1).toBeTruthy();

    const parser2 = new Parser('https://mnogomeb.ru');
    const hasCatalog2 = await parser2.hasCatalog();
    expect(hasCatalog2).toBeTruthy();

    const parser3 = new Parser('https://portal.skillline.ru');
    const hasCatalog3 = await parser3.hasCatalog();
    expect(hasCatalog3).toBeFalsy();

    const parser4 = new Parser('https://skillline.ru');
    const hasCatalog4 = await parser4.hasCatalog();
    expect(hasCatalog4).toBeFalsy();
}, 20000);


test('check if there is cart on "Bitrix" website', async () => {
    const parser1 = new Parser(unipumpUrl);
    const hasCatalog1 = await parser1.hasCart();
    expect(hasCatalog1).toBeTruthy();

    const parser2 = new Parser('https://mnogomeb.ru');
    const hasCatalog2 = await parser2.hasCart();
    expect(hasCatalog2).toBeTruthy();

    const parser3 = new Parser('https://portal.skillline.ru');
    const hasCatalog3 = await parser3.hasCart();
    expect(hasCatalog3).toBeFalsy();

    const parser4 = new Parser('https://skillline.ru');
    const hasCatalog4 = await parser4.hasCart();
    expect(hasCatalog4).toBeFalsy();
}, 20000);

test('get info from db', async () => {
    // TODO: Сделать когда будет api
});

test('send info to db', async () => {
    // TODO: Сделать когда будет api
});
