const Parser = require('../modules/parser');
const functions = require('../modules/functions');
const Client = require('../modules/request');
const Logger = require('../modules/logger');
const {AxiosResponse} = require('axios');

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
});

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
});

test('parser check https redirect', async () => {
    const parser1 = new Parser(httpUrl1);
    const response1 = await parser1.makeHttpRequest(parser1.getDomain());
    expect(parser1.checkHttpsRedirect(response1)).toBeFalsy();

    const parser2 = new Parser('http://' + unipumpDomain);
    const response2 = await parser2.makeHttpRequest(parser2.getDomain());
    expect(parser2.checkHttpsRedirect(response2)).toBeTruthy();
});

test('parser check SSL', async () => {
    const parser1 = new Parser(httpUrl1);
    const response1 = await parser1.makeHttpRequest(parser1.getDomain());
    expect(parser1.checkSsl(response1)).toBeFalsy();

    const parser2 = new Parser(unipumpUrl);
    const response2 = await parser2.makeHttpRequest(parser2.getDomain());
    expect(parser2.checkSsl(response2)).toBeTruthy();
});

test('parser get html', async () => {
    const parser = new Parser(unipumpUrl);
    const response = await parser.makeHttpsRequest(parser.getDomain());
    expect(parser.getHtml(response).length).toBeGreaterThan(1000);
});

test('get info from db', async () => {
    // TODO: Сделать когда будет api
});

test('send info to db', async () => {
    // TODO: Сделать когда будет api
});
