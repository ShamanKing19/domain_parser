const {AxiosResponse} = require('axios');
const { parse } = require('node-html-parser');

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
     * @returns {HTMLElement}
     */
    getHtml(response) {
        return parse(response ? response.data : '');
    }

    /**
     * Получение заголовка сайта
     *
     * @param {HTMLElement} html
     * @returns {string}
     */
    getTitle(html) {
        let title = html.querySelector('title');
        if(title && title.innerText) {
            return title.innerText;
        }

        title = html.querySelector('meta[property="title"]');
        if(title) {
            return title.getAttribute('content');
        }

        title = html.querySelector('meta[property="og:title"]');
        if(title) {
            return title.getAttribute('content');
        }

        return '';
    }

    /**
     * Получение описания сайта
     *
     * @param {HTMLElement} html
     * @returns {string}
     */
    getDescription(html) {
        let description = html.querySelector('meta[name="description"]');
        if(description) {
            return description.getAttribute('content');
        }

        description = html.querySelector('meta[property="og:description"]');
        if(description) {
            return description.getAttribute('content');
        }

        return '';
    }

    /**
     * Получение ключевых слов сайта
     *
     * @param {HTMLElement} html
     * @returns {string}
     */
    getKeywords(html) {
        let keywords = html.querySelector('meta[name="keywords"]');
        if(keywords) {
            return keywords.getAttribute('content');
        }

        keywords = html.querySelector('meta[property="og:keywords"]');
        if(keywords) {
            return keywords.getAttribute('content');
        }

        return '';
    }

    /**
     * Получение названия используемой CMS, если она используется
     *
     * @param {HTMLElement} html
     * @returns {string}
     */
    guessCms(html) {
        const cmsExamples = {
            'src="/bitrix/': 'bitrix',
            'href="/bitrix': 'bitrix',
            'bitrix/templates/': 'bitrix',
            'bitrix/cache/': 'bitrix',
            'wp-content/': 'wordpress',
            'wp-includes/': 'wordpress',
            '<meta name="modxru': 'modx',
            '<script type="text/javascript" src="/netcat': 'netcat',
            '<script src="/phpshop': 'phpshop',
            '<script type="text/x-magento-init': 'magento',
            '/wa-data/': 'shop-script',
            'catalog/view/': 'opencart',
            'data-drupal-': 'drupal',
            'name="generator" content="Joomla': 'joomla',
            '/media/system': 'joomla',
            'var dle_admin': 'datalife engine',
            'UCOZ-JS': 'ucoz',
            'ucoz.net/': 'ucoz',
            '<script src="https://static.tilda': 'tilda',
            '<meta name="generator" content="Wix': 'wix',
            'type="wix/htmlEmbeds"': 'wix',
            'nethouse.ru/': 'nethouse',
            'data-muse-uid': 'adobe muse',
            'museutils': 'adobe muse',
            'xmlns:umi="http://www.umi-cms.ru': 'umi',
            'img src="/images/cms/': 'umi',
            '-= Amiro.CMS (c) =-': 'amiro',
            'amiro_sys_': 'amiro',
            'content="CMS EDGESTILE SiteEdit">': 'siteedit',
            'meta name="generator" content="OkayCMS': 'okay',
            '/_nuxt': 'nuxt'
        };

        for(const string in cmsExamples) {
            const cms = cmsExamples[string];
            if(html.innerHTML.includes(string.replace('/\//g', '\/'))) {
                return cms;
            }
        }

        return '';
    }

    /**
     * Поиск ИНН'ов
     *
     * @param {HTMLElement} html
     * @returns {string[]}
     */
    findInns(html) {

    }

    /**
     * Поиск номеров телефонов
     *
     * @param {HTMLElement} html
     * @returns {string[]}
     */
    findPhones(html) {

    }

    /**
     * Поиск электронных почт
     *
     * @param {HTMLElement} html
     * @returns {string[]}
     */
    findEmails(html) {

    }

    /**
     * Поиск адресов
     *
     * @param {HTMLElement} html
     * @returns {string[]}
     */
    findAddresses(html) {

    }

    /**
     * Получение категории сайта
     *
     * @param {HTMLElement} html
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
