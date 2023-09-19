const Parser = require('../modules/parser');

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

const unipumpUrl = 'https://unipump.ru';


test('parser instance is created', () => {

    const parser = new Parser(urlList[0]);
    expect(parser).toBeInstanceOf(Parser);
});

test('set domain instead of url', () => {
    const domain = 'domain.com/';
    const parser = new Parser(domain);
    expect(parser.getDomain()).toBe('domain.com');
    expect(parser.getUrl()).toBe('https://domain.com');
});

test('get url', () => {
    const parser = new Parser(testUrl1);
    expect(parser.getUrl()).toBe('https://jestjs.io/docs/getting-started');
});

test('get domain', () => {
    const parser1 = new Parser(testUrl1);
    expect(parser1.getDomain()).toBe('jestjs.io/docs/getting-started');

    const parser2 = new Parser(testUrl2);
    expect(parser2.getDomain()).toBe('www.npmjs.com/package/node-html-parser');

    const parser3 = new Parser(testUrl3);
    expect(parser3.getDomain()).toBe('www.dev-notes.ru/articles');
});

test('get id', () => {
    const parser1 = new Parser(testUrl1, 1);
    expect(parser1.getId()).toBe(1);

    const parser2 = new Parser(testUrl2);
    expect(parser2.getId()).toBe(0);
});

test('get title', async() => {
    const parser = new Parser(unipumpUrl);
    await parser.init();

    const html = parser.getHtml();

    expect(parser.getTitle(html)).toBe('Насосы и насосное оборудование от производителя UNIPUMP');
});

test('get description', async () => {
    const parser = new Parser(unipumpUrl);
    await parser.init();

    const html = parser.getHtml();
    expect(parser.getDescription(html)).toBe('Компания UNIPUMP занимается производством насосов и насосного оборудования для водоснабжения, отопления, дренажа и канализации.');
});

test('get keywords', async () => {
    const parser = new Parser(unipumpUrl);
    await parser.init();

    const html = parser.getHtml();
    expect(parser.getKeywords(html)).toBe('Keywords'); // Да, оно у них не заполнено)))
});
