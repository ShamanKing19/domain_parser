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
        this.itemsPerPage = 100;
    }

    async run() {
        let currentPage = 1;
        const lastPage = await this.getLastPageNumber(this.itemsPerPage);
        const domainsCount = await this.getDomainsCount();
        let domainList = await this.getDomains(1, this.itemsPerPage);

        while(currentPage <= lastPage) {
            const start = Date.now();

            const nextPage = currentPage === lastPage ? 1 : currentPage + 1;
            const nextPageDomainsList = this.getDomains(nextPage, this.itemsPerPage);
            let parsedData = [];
            let parsers = [];
            for(const domainItem of domainList) {
                const parser = new Parser(domainItem['domain'], domainItem['id']);
                parsers.push(parser);
            }

            // 1. Проверка статусов
            parsers = await Promise.all(parsers.map(parser => parser.checkStatus()));
            const validSiteCount = parsers.filter(parser => parser.response && parser.response.status === 200)
            // console.log('1 -', this.now(start), `- (${validSiteCount.length}/${parsers.length})`, '- status');

            // 2. Проверка https редиректов
            parsers = await Promise.all(parsers.map(parser => parser.checkRedirect()));
            // console.log('2 -', this.now(start), '- redirect');

            // 3. Сбор информации без запросов
            parsers = parsers.map(parser => parser.parse());
            // console.log('3 -', this.now(start), '- parsing');

            // 4. Проверка битриксовых сайтов на каталог и корзину
            parsers = await Promise.all(parsers.map(parser => parser.checkBitrixEcom()));
            // console.log('4 -', this.now(start), '- bitrix ecom');

            // 5. Поиск информации по ИНН
            parsers = await Promise.all(parsers.map(parser => parser.collectCompanyInfo()));
            // console.log('5 -', this.now(start), '- company info');

            parsedData = parsers.map(parser => parser.toObject());

            await this.sendParsedData({'domains': parsedData})
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

            await this.logger.log(`${this.now(start)} - (${validSiteCount.length}/${parsers.length}) - Обработано ${currentPage} из ${lastPage} страниц (${currentPage * this.itemsPerPage}/${domainsCount})`, true);

            currentPage++;
            domainList = await nextPageDomainsList;
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
            },
            timeout: 0
        });

        return response ? response.data['data'] : false;
    }

    now(start) {
        return Date.now() / 1000 - start / 1000;
    }
}

module.exports = App;
