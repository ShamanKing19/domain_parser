const Parser = require('../modules/parser');
const functions = require('../modules/functions');
const oldClient = require('../modules/request');
const Client = require('../modules/client');
const Logger = require('../modules/logger');
const got = require('got');
const { AxiosResponse } = require('axios');
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


test('parser make 1st step', async () => {
    const parser = new Parser(httpUrl2);
    await parser.init();
    expect(parser.status).toBe(200);
    expect(parser.realUrl).toBe('https://www.go.com/');
    expect(parser.hasWwwRedirect).toBeTruthy();
    expect(parser.hasSsl).toBeTruthy();
});

test('check if there is catalog on "Bitrix" website', async () => {
    const domainsWithCatalog = [unipumpUrl, 'www.ascgroup.ru', 'https://mnogomeb.ru'];
    const domainsWithoutCatalog = ['https://skillline.ru'];

    for(const domain of domainsWithCatalog) {
        const parser = new Parser(domain);
        const hasCatalog = await parser.checkIfHasCatalog();
        expect(hasCatalog).toBeTruthy();
    }

    for(const domain of domainsWithoutCatalog) {
        const parser = new Parser(domain);
        const hasCatalog = await parser.checkIfHasCatalog();
        expect(hasCatalog).toBeFalsy();
    }
}, 20000);


test('check if there is cart on "Bitrix" website', async () => {
    const domainsWithCart = [unipumpUrl, 'mnogomeb.ru'];
    const domainsWithoutCart = ['portal.skillline.ru', 'skillline.ru'];

    for(const domain of domainsWithCart) {
        const parser = new Parser(domain);
        const hasCatalog = await parser.checkIfHasCart();
        expect(hasCatalog).toBeTruthy();
    }

    for(const domain of domainsWithoutCart) {
        const parser = new Parser(domain);
        const hasCatalog = await parser.checkIfHasCart();
        expect(hasCatalog).toBeFalsy();
    }
}, 20000);

test('get info from db', async () => {
    // TODO: Сделать когда будет api
});

test('send info to db', async () => {
    // TODO: Сделать когда будет api
});
