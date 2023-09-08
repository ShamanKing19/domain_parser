const Parser = require('./modules/parser');
const Logger = require('./modules/logger');
const Functions = require('./modules/functions');
const Client = require('./modules/request');
const { AxiosResponse } = require('axios');

class App
{
    constructor() {
        this.apiUrl = '/api';
        this.logger = new Logger();
        this.function = new Functions();
        this.client = new Client();
    }

    async run() {
        const itemsPerPage = 300;
        let currentPage = 1;
        const lastPage = await this.getLastPageNumber(itemsPerPage);

        while(currentPage <= lastPage) {
            const domainList = await this.getDomains(currentPage, itemsPerPage);
            const parsedData = [];
            for(const domain of domainList) {
                const parser = new Parser(domain);
                parsedData.push(parser.run());
            }

            await Promise.all(parsedData);

            const response  = this.sendParsedData(parsedData).then(async (response) => {
                const pageNumber = currentPage.data['page_number'] ?? currentPage;
                const now = this.function.getCurrentDate();
                await this.logger.log(`[${now}]: Порция ${pageNumber} отправлена`);
            });

            currentPage++;
            if(currentPage === lastPage) {
                await response;
                currentPage = 1;
            }
        }
    }

    /**
     * Отправка данных по API
     *
     * @param data
     * @return {Promise<AxiosResponse>}
     */
    async sendParsedData(data) {
        return this.client.post(this.apiUrl + '/some_path', data);
    }

    /**
     * Список ссылок
     *
     * @param {number} pageNumber
     * @param {number} itemsPerPage
     * @return {Promise<string[]>}
     */
    async getDomains(pageNumber, itemsPerPage) {
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

    /**
     * Номер последней страницы
     *
     * @param {number} itemsPerPage
     * @return {Promise<number>}
     */
    async getLastPageNumber(itemsPerPage) {
        return 10;
    }
}

module.exports = App;
