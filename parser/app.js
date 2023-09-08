const Parser = require('./modules/parser');

class App
{
    async run() {
        const domainList = await this.getDomains();
        const parserList = [];
        for(const domain of domainList) {
            const parser = new Parser(domain);
            parserList.push(parser.run());
        }

        await Promise.all(parserList);
    }

    async getDomains() {
        return [
            'https://jestjs.io/docs/getting-started',
            'https://www.npmjs.com/package/node-html-parser',
            'https://www.dev-notes.ru/articles/',
            'https://habr.com/ru/companies/trinion/articles/315538/',
            'https://www.1c-bitrix.ru/',
            'https://klgtu.ru/',
            'https://kantiana.ru/',
        ];
    }
}

module.exports = App;
