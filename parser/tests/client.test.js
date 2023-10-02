const Client = require("../modules/client");

const unipumpDomain = 'unipump.ru';
const unipumpUrl = 'https://' + unipumpDomain;


test('get 200 response via GET method', async () => {
    // const client = new oldClient();
    const client = new Client('https://google.com');
    await client.get();

    expect(client.getStatus()).toBe(200);
    expect(client.getStatusText()).toBe('OK');
    expect(client.getBody().length).toBeGreaterThan(0);

    const client2 = new Client('https://reqres.in/api/users');
    await client2.get();
    expect(client2.getJson()).toBeInstanceOf(Object);
});

test('get 201 response via POST method', async () => {
    const client = new Client('https://reqres.in/api/users');
    await client.post({
        'name': 'morpheus',
        'job': 'leader'
    });

    expect(client.getStatus()).toBe(201);
    expect(client.getStatusText()).toBe('Created');
    expect(client.getJson()).toBeInstanceOf(Object);
});

test('get 404 response vie GET method', async () => {
    const client = new Client(unipumpUrl + '/ohuuiho232ohi1h');
    await client.get();

    expect(client.getStatus()).toBe(404);
    expect(client.getStatusText()).toBe('Not Found');

    const url2 = 'https://000SB.RU';
    const client2 = new Client(url2);
    await client2.get();

    expect(client2.getStatus()).toBe(404);
    expect(client2.getStatusText()).toBe('Not Found');
});

test('handle timeout', async () => {
    const url = 'https://germes-dent.ru/';
    const client = new Client(url, {}, 100);
    await client.get();

    expect(client.getStatus()).toBe(408);
}, 10000);

test('make request to non-existing url via GET method', async () => {
    const client = new Client('https://klhj23h5ljk1jrhlk.zxc');
    await client.get();
    expect(client.getStatus()).toBe(404);
});

test('make request to website with cyrillic url ', async () => {
    const brokenurl = 'https://kanalizaciya-prom.ru/';

    const client = new Client(brokenurl);
    await client.get();

    expect(client.getStatus()).toBe(200);
    expect(client.getBody().length).toBeGreaterThan(0);
});
