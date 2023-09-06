const {AxiosResponse} = require('axios');

class Parser
{
    functions = require('./functions');
    request = require('./request');


    /**
     * @param {string} url Ссылка на сайт
     * @param {number} id ID записи в базе данных
     */
    constructor(url, id = 0) {
        this.url = url;
        this.id = id;
        this.client = new this.request();
    }

    /**
     * Получение ссылки на сайт
     *
     * @returns {string}
     */
    getUrl() {
        return this.url;
    }

    /**
     * Получение домена (без протокола)
     *
     * @returns {string}
     */
    getDomain() {
        return this.url.split('://').pop();
    }

    /**
     * Получение id записи из базы данных
     *
     * @returns {number}
     */
    getId() {
        return this.id;
    }

    /**
     * Парсинг сайта
     *
     * @returns {Promise<void>}
     */
    async parse() {
        const domain = this.getDomain();

        const httpRequest = this.makeHttpRequest(domain);
        const httpsRequest = this.makeHttpsRequest(domain);

        const result = await Promise.all([httpRequest, httpsRequest]);
        const httpResponse = result[0];
        const httpsResponse = result[1];

        const hasHttpsRedirect = this.checkHttpsRedirect(httpsResponse);
        const hasSsl = this.checkSsl(httpsResponse);

        const status = this.getStatusCode(httpsResponse);
        const realUrl = this.getRealUrl(httpsResponse);
        const html = this.getHtml(httpsResponse);

        const title = this.getTitle(html);
        const description = this.getDescription(html);
        const keywords = this.getKeywords(html);

        const cms = this.guessCms(html);
        const innList = this.findInns(html);
        const phoneList = this.findPhones(html);
        const emailList = this.findEmails(html);
        const addressList = this.findAddresses(html);

        const category = this.guessCategory(html);

        const finances = innList.length !== 0 ? await this.findFinanceInfo(innList) : {};
    }

    /**
     * Запрос по протоколу HTTP
     *
     * @param domain Домен
     * @returns {AxiosResponse|false}
     */
    async makeHttpRequest(domain)
    {
        return await this.client.get('http://' + domain);
    }

    /**
     * Запрос по протоколу HTTPS
     *
     * @param domain Домен
     * @returns {AxiosResponse|false}
     */
    async makeHttpsRequest(domain) {
        return await this.client.get('https://' + domain);
    }

    /**
     * Проверка: есть ли у сайта SSL сертификат
     *
     * @param {AxiosResponse} response
     * @returns {boolean}
     */
    checkSsl(response) {
        const responseUrl = response.request.res.responseUrl;

        return responseUrl.includes('https://');
    }

    /**
     * Проверка: есть ли у сайта редирект на https
     *
     * @param {AxiosResponse} response
     * @returns {boolean}
     */
    checkHttpsRedirect(response) {
        const requestUrl = response.config.url;
        if(requestUrl.includes('https://')) {
            return false;
        }

        const responseUrl = response.request.res.responseUrl;

        return responseUrl.includes('https://');
    }

    /**
     * Получение http-кода ответа
     *
     * @param {AxiosResponse} response
     * @returns {number}
     */
    getStatusCode(response) {
        return response.status
    }

    /**
     * Получение настоящей ссылки (после всех редирект ов)
     *
     * @param {AxiosResponse} response
     * @returns {string}
     */
    getRealUrl(response) {
        return response.request.res.responseUrl;
    }

    /**
     * Получение html из ответа
     *
     * @param {AxiosResponse} response
     * @returns {string}
     */
    getHtml(response) {
        return response.data
    }

    /**
     * Получение заголовка сайта
     *
     * @param html
     * @returns {string}
     */
    getTitle(html) {

    }

    /**
     * Получение описания сайта
     *
     * @param html
     * @returns {string}
     */
    getDescription(html) {

    }

    /**
     * Получение ключевых слов сайта
     *
     * @param html
     * @returns {string}
     */
    getKeywords(html) {

    }

    /**
     * Получение названия используемой CMS, если она используется
     *
     * @param html
     * @returns {string}
     */
    guessCms(html) {

    }

    /**
     * Поиск ИНН'ов
     *
     * @param html
     * @returns {string[]}
     */
    findInns(html) {

    }

    /**
     * Поиск номеров телефонов
     *
     * @param html
     * @returns {string[]}
     */
    findPhones(html) {

    }

    /**
     * Поиск электронных почт
     *
     * @param html
     * @returns {string[]}
     */
    findEmails(html) {

    }

    /**
     * Поиск адресов
     *
     * @param html
     * @returns {string[]}
     */
    findAddresses(html) {

    }

    /**
     * Получение категории сайта
     *
     * @param html
     * @returns {string}
     */
    guessCategory(html) {

    }

    /**
     * Получение финансовых данных по ИНН
     *
     * @param {string[]} innList
     * @return {Promise<Awaited<unknown>[]>}
     */
    async findFinanceInfo(innList) {
        const requests = [];
        for(const inn of innList) {
            innList.push(this.findFinanceInfoByInn(inn));
        }

        return await Promise.all(requests);
    }

    /**
     * Получение финансовых данных по ИНН
     *
     * @param {string} inn
     * @return {Promise<Awaited<unknown>[]>}
     */
    async findFinanceInfoByInn(inn) {

    }

    /**
     * Сохранение данных о сайте в базу
     *
     * @returns {Promise<void>}
     */
    async saveToDb() {

    }

    /**
     * Получение информации о сайте с базы
     *
     * @returns {Promise<void>}
     */
    async getFromDb() {

    }


}

module.exports = Parser;
