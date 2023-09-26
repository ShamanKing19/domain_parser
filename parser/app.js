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
        this.itemsPerPage = 50;
    }

    /**
     * Запуск парсера с учётом параметров
     *
     * @param args
     * @return {Promise<void>}
     */
    async runWithParams(args) {
        const params = this.parseArgs(args);
        if(params['domain']) {
            const parser = new Parser(params['domain']);
            await parser.init();
            await parser.checkRedirect();
            await parser.parse();
            await parser.checkBitrixEcom();
            await parser.collectCompanyInfo();
            const response = await this.sendParsedDomain(parser.toObject());
            console.log(JSON.stringify(response.data)); // Для возврата ответа при вызове из админки через exec()
        }

    }

    /**
     * Парсинг параметров
     *
     * @param {string[]} args Параметры запуска
     * @return {object}
     */
    parseArgs(args) {
        const regex = /--(.*[^\s])/g;
        const paramsObject = {};
        for(const arg of args) {
            const params = arg.match(regex);
            if(!params || params.length === 0) {
                continue;
            }

            for(const param of params) {
                const paramArray = param.split('=');
                if(paramArray.length !== 2) {
                    continue;
                }

                const paramName = paramArray[0].replace('--', '');
                paramsObject[paramName] = paramArray[1];
            }
        }

        return paramsObject;
    }

    async run() {
        let currentPage = 1;
        const lastPage = await this.getLastPageNumber(this.itemsPerPage);
        const domainsCount = await this.getDomainsCount();
        let domainList = await this.getDomains(currentPage, this.itemsPerPage);

        while(currentPage <= lastPage) {
            const start = Date.now();
            const nextPage = currentPage === lastPage ? 1 : currentPage + 1;
            const nextPageDomainsList = this.getDomains(nextPage, this.itemsPerPage);
            let parsedData = [];
            let parsers = [];
            for(const domainItem of domainList) {
                parsers.push(new Parser(domainItem['domain'], domainItem['id']));
            }

            // 1. Проверка статусов
            parsers = await Promise.all(parsers.map(parser => parser.init()));
            parsers = parsers.filter(parser => parser.hasResponse());
            // console.log('1 -', this.timeSpent(start), `- (${parsers.length}/${domainList.length})`, '- status');

            // 2. Проверка https редиректов
            parsers = await Promise.all(parsers.map(parser => parser.checkRedirect()));
            // console.log('2 -', this.timeSpent(start), '- redirect');

            // 3. Сбор информации без запросов
            parsers = parsers.map(parser => parser.parse());
            // console.log('3 -', this.timeSpent(start), '- parsing');

            // 4. Проверка битриксовых сайтов на каталог и корзину
            parsers = await Promise.all(parsers.map(parser => parser.checkBitrixEcom()));
            // console.log('4 -', this.timeSpent(start), '- bitrix ecom');

            // 5. Поиск информации по ИНН
            parsers = await Promise.all(parsers.map(parser => parser.collectCompanyInfo()));
            // console.log('5 -', this.timeSpent(start), '- company info');

            parsedData = parsers.map(parser => parser.toObject());

            if(parsedData.length !== 0) {
                const response = await this.sendParsedData({'domains': parsedData});
                if(!response) {
                    this.logger.logJson('broken_data/' + currentPage, parsedData);
                    await this.logger.error(`Ошибка при отправке запроса на api`, true)
                }

                if(!response || !response.data || !response.data['status']) {
                    await this.logger.logJson('broken_api_data/' + currentPage, parsedData);
                    await this.logger.error(JSON.stringify(response.data), true);
                }
            }

            await this.logger.log(`${this.timeSpent(start)} - (${parsers.length}/${domainList.length}) - Обработано ${currentPage} из ${lastPage} страниц (${currentPage * this.itemsPerPage}/${domainsCount})`, true);

            currentPage++;
            domainList = null;
            parsers = null;
            parsedData = null;
            domainList = await nextPageDomainsList;
        }
    }

    /**
     * Отправка данных по одному домену
     *
     * @param data
     * @return {Promise<AxiosResponse>}
     */
    async sendParsedDomain(data) {
        return this.client.post(this.apiUrl + '/update', data, {
            timeout: 0
        });
    }

    /**
     * Отправка данных по нескольким доменам
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

    timeSpent(start) {
        return Math.round((Date.now() - start) / 10) / 100;
    }
}

module.exports = App;
