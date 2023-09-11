const Parser = require('./modules/parser');
const Logger = require('./modules/logger');
const Functions = require('./modules/functions');
const Client = require('./modules/request');
const { AxiosResponse } = require('axios');

class App
{
    constructor() {
        // this.apiUrl = '/api/domain';
        this.apiUrl = 'https://domainsparse.dev.skillline.ru/api/domain';
        this.logger = new Logger();
        this.function = new Functions();
        this.client = new Client();
        this.itemsPerPage = 300;
    }

    async run() {
        let currentPage = 1;
        const lastPage = await this.getLastPageNumber(this.itemsPerPage);

        while(currentPage <= lastPage) {
            const domainList = await this.getDomains(currentPage, this.itemsPerPage);
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
     * Номер последней страницы
     *
     * @param {number} itemsPerPage
     * @return {Promise<AxiosResponse>}
     */
    async getLastPageNumber(itemsPerPage) {
        return
    }

    /**
     * Список ссылок
     *
     * @param {number} pageNumber
     * @param {number} itemsPerPage
     * @return {Promise<object>}
     */
    async getDomains(pageNumber, itemsPerPage) {
        const data = await this.sendDomainsRequest(pageNumber, itemsPerPage);

        return data['data']['data'];
    }

    /**
     * Запрос на получение доменов
     *
     * @param pageNumber
     * @param itemsPerPage
     * @return {Promise<object>}
     */
    async sendDomainsRequest(pageNumber, itemsPerPage) {
        const response = await this.client.get(this.apiUrl, {
            params: {
                'page': pageNumber,
                'count': itemsPerPage
            }
        });

        return response.data;
    }
}

module.exports = App;
