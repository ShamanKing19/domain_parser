const Parser = require('../modules/parser');
const Client = require("../modules/request");

const testUrl = 'zxc';

test('inn validation', () => {
    const parser = new Parser(testUrl);

    const validInnList = ['3906390130', '7730588444', '9500018482', '9500018475', '9500018468', '972714924120', '971511159105'];
    const invalidInnList = ['3906390131', '3806390130', '38063901300', '123231123', 'adsqwwqdqdqdw'];

    for(const inn of validInnList) {
        expect(parser.isInnValid(inn)).toBeTruthy();
    }

    for(const inn of invalidInnList) {
        expect(parser.isInnValid(inn)).toBeFalsy();
    }
});


test('find inns', () => {
    const parser = new Parser(testUrl);
    const text = `

    `;

    const innList = parser.findInns(text);
    const correctResult = ['', '', '', '', ''];

    expect(innList).toStrictEqual(correctResult);
});

test('find phones', () => {
    const parser = new Parser(testUrl);

});

test('find emails', () => {
    const parser = new Parser(testUrl);

});

test('find address', () => {
    const parser = new Parser(testUrl);

});

test('guess category', () => {
    const parser = new Parser(testUrl);

});
