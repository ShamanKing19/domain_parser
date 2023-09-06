const Parser = require('../modules/parser');

const unipumpUrl = 'https://unipump.ru';

test('parser guess Bitrix', async () => {
    const parser = new Parser(unipumpUrl);
    const response = await parser.makeHttpsRequest(parser.getDomain());
    const html = parser.getHtml(response);
    expect(parser.guessCms(html)).toBe('bitrix');
});

test('parser guess Wordpress', async () => {
    const parser = new Parser('');
    const response = await parser.makeHttpsRequest(parser.getDomain());
    const html = parser.getHtml(response);
    expect(parser.guessCms(html)).toBe('wordpress');
});

test('parser guess Tilda', async () => {
    const parser = new Parser('');
    const response = await parser.makeHttpsRequest(parser.getDomain());
    const html = parser.getHtml(response);
    expect(parser.guessCms(html)).toBe('tilda');
});

test('parser guess Joomla', async () => {
    const parser = new Parser('');
    const response = await parser.makeHttpsRequest(parser.getDomain());
    const html = parser.getHtml(response);
    expect(parser.guessCms(html)).toBe('joomla');
});

test('parser guess OpenCart', async () => {
    const parser = new Parser('');
    const response = await parser.makeHttpsRequest(parser.getDomain());
    const html = parser.getHtml(response);
    expect(parser.guessCms(html)).toBe('opencart');
});

test('parser guess Wix', async () => {
    const parser = new Parser('');
    const response = await parser.makeHttpsRequest(parser.getDomain());
    const html = parser.getHtml(response);
    expect(parser.guessCms(html)).toBe('wix');
});

test('parser guess Shop-Script', async () => {
    const parser = new Parser('');
    const response = await parser.makeHttpsRequest(parser.getDomain());
    const html = parser.getHtml(response);
    expect(parser.guessCms(html)).toBe('shop-script');
});

test('parser guess DataLife Engine', async () => {
    const parser = new Parser('');
    const response = await parser.makeHttpsRequest(parser.getDomain());
    const html = parser.getHtml(response);
    expect(parser.guessCms(html)).toBe('datalife engine');
});

test('parser guess Ucoz', async () => {
    const parser = new Parser('');
    const response = await parser.makeHttpsRequest(parser.getDomain());
    const html = parser.getHtml(response);
    expect(parser.guessCms(html)).toBe('ucoz');
});

test('parser guess Nethouse', async () => {
    const parser = new Parser('');
    const response = await parser.makeHttpsRequest(parser.getDomain());
    const html = parser.getHtml(response);
    expect(parser.guessCms(html)).toBe('nethouse');
});

test('parser guess Adobe Muse', async () => {
    const parser = new Parser('');
    const response = await parser.makeHttpsRequest(parser.getDomain());
    const html = parser.getHtml(response);
    expect(parser.guessCms(html)).toBe('adobe muse');
});

test('parser guess UMI', async () => {
    const parser = new Parser('');
    const response = await parser.makeHttpsRequest(parser.getDomain());
    const html = parser.getHtml(response);
    expect(parser.guessCms(html)).toBe('umi');
});

test('parser guess Drupal', async () => {
    const parser = new Parser('');
    const response = await parser.makeHttpsRequest(parser.getDomain());
    const html = parser.getHtml(response);
    expect(parser.guessCms(html)).toBe('drupal');
});

test('parser guess Okay', async () => {
    const parser = new Parser('');
    const response = await parser.makeHttpsRequest(parser.getDomain());
    const html = parser.getHtml(response);
    expect(parser.guessCms(html)).toBe('okay');
});

test('parser guess PhpShop', async () => {
    const parser = new Parser('');
    const response = await parser.makeHttpsRequest(parser.getDomain());
    const html = parser.getHtml(response);
    expect(parser.guessCms(html)).toBe('phpshop');
});

test('parser guess Amiro', async () => {
    const parser = new Parser('');
    const response = await parser.makeHttpsRequest(parser.getDomain());
    const html = parser.getHtml(response);
    expect(parser.guessCms(html)).toBe('amiro');
});

test('parser guess Netcat', async () => {
    const parser = new Parser('');
    const response = await parser.makeHttpsRequest(parser.getDomain());
    const html = parser.getHtml(response);
    expect(parser.guessCms(html)).toBe('netcat');
});

test('parser guess SiteEdit', async () => {
    const parser = new Parser('');
    const response = await parser.makeHttpsRequest(parser.getDomain());
    const html = parser.getHtml(response);
    expect(parser.guessCms(html)).toBe('siteedit');
});

test('parser guess ModX', async () => {
    const parser = new Parser('');
    const response = await parser.makeHttpsRequest(parser.getDomain());
    const html = parser.getHtml(response);
    expect(parser.guessCms(html)).toBe('modx');
});

test('parser guess Magento', async () => {
    const parser = new Parser('');
    const response = await parser.makeHttpsRequest(parser.getDomain());
    const html = parser.getHtml(response);
    expect(parser.guessCms(html)).toBe('magento');
});
