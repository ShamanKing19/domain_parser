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
        const domainsCount = await this.getDomainsCount();

        while(currentPage <= lastPage) {
            const domainList = await this.getDomains(currentPage, this.itemsPerPage);
            let parsedData = [];
            for(const domainItem of domainList) {
                const parser = new Parser(domainItem['domain'], domainItem['id']);
                parsedData.push(parser.run());
            }

            parsedData = await Promise.all(parsedData);
            const request = this.sendParsedData({'domains': parsedData})
                .then(async (response) => {
                    if(!response) {
                        this.logger.logJson('broken_data/' + currentPage, parsedData);
                        await this.logger.error(`Ошибка при отправке запроса на api`, true)
                        // continue;
                    }

                    if(!response || !response.data) {
                        await this.logger.logJson('broken_api_data/' + currentPage, parsedData);
                        await this.logger.error('Ошибка при получении ответа от api');
                        // continue;
                    }
                });

            await this.logger.log(`Спаршено ${currentPage} из ${lastPage} страниц (${currentPage * this.itemsPerPage}/${domainsCount})`, true);

            currentPage++;
            if(currentPage === lastPage) {
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
        return this.client.post(this.apiUrl + '/update-many', data, {
            timeout: 0
        });
    }

    /**
     * Количество доменов
     *
     * @return {Promise<number>}
     */
    async getDomainsCount() {
        const response = await this.sendDomainsRequest(1, 1);

        return response['total'] ?? 0;
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
        if(!data) {
            return [];
        }

        return data['data'];
    }

    /**
     * Запрос на получение доменов
     *
     * @param pageNumber
     * @param itemsPerPage
     * @return {Promise<object>|false}
     */
    async sendDomainsRequest(pageNumber, itemsPerPage) {
        const response = await this.client.get(this.apiUrl, {
            params: {
                'page': pageNumber,
                'count': itemsPerPage
            }
        });

        return response ? response.data['data'] : false;
    }
}

module.exports = App;
