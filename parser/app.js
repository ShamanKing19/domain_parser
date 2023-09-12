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
        this.itemsPerPage = 10;
    }

    async run() {
        let currentPage = 1;
        const lastPage = await this.getLastPageNumber(this.itemsPerPage);

        while(currentPage <= lastPage) {
            const domainList = await this.getDomains(currentPage, this.itemsPerPage);
            let parsedData = [];
            for(const domainItem of domainList) {
                const parser = new Parser(domainItem['domain'], domainItem['id']);
                parsedData.push(parser.run());
            }

            parsedData = await Promise.all(parsedData);
            const response  = await this.sendParsedData({'domains': parsedData});

            if(!response) {
                this.logger.logJson('parsedData', parsedData);
                await this.logger.error(`Ошибка при отправке запроса на api`, true)
                continue;
            }

            // this.logger.logJson('responseData', response.data);
            // this.logger.logJson('sentData', {'domains': parsedData});

            const pageNumber = response.data['page_number'] ?? currentPage;
            const now = this.function.getCurrentDate();
            await this.logger.log(`[${now}]: Порция ${pageNumber} отправлена`, true);

            currentPage++;
            if(currentPage === lastPage) {
                // await response;
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
        return this.client.post(this.apiUrl + '/update-many', data);
    }

    /**
     * Номер последней страницы
     *
     * @param {number} itemsPerPage
     * @return {Promise<number>}
     */
    async getLastPageNumber(itemsPerPage) {
        const response = await this.sendDomainsRequest(1, itemsPerPage);

        return response['last_page'];
    }

    /**
     * Список доменов с id
     *
     * @param {number} pageNumber
     * @param {number} itemsPerPage
     * @return {Promise<object>}
     */
    async getDomains(pageNumber, itemsPerPage) {
        const data = await this.sendDomainsRequest(pageNumber, itemsPerPage);

        return data['data'];
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

        return response.data['data'];
    }
}

module.exports = App;
