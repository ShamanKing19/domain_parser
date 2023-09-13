const {AxiosResponse} = require('axios');
const { parse } = require('node-html-parser');
const Functions = require('./functions');
const Client = require('./request');
const Company = require('./company_parser');

class Parser
{
    /**
     * @param {string} url Ссылка на сайт
     * @param {number} id ID записи в базе данных
     */
    constructor(url, id = 0) {
        this.url = url;
        this.id = id;
        this.client = new Client();
        this.functions = new Function();
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
     * @returns {Promise<>}
     */
    async run() {
        const domain = this.getDomain();

        const httpRequest = this.makeHttpRequest(domain);
        let httpsRequest = this.makeHttpsRequest(domain);
        const result = await Promise.all([httpsRequest, httpRequest]);
        let httpsResponse = result[0];
        const httpResponse = result[1];

        const hasHttpsRedirect = httpResponse ? this.checkHttpsRedirect(httpResponse) : false;
        if(!httpResponse && !httpsResponse) {
            return {
                id: this.id,
                status: 0
            }
        }

        if(!httpsResponse && httpResponse) {
            httpsResponse = httpResponse;
        }

        const status = this.getStatusCode(httpsResponse);
        const realUrl = this.getRealUrl(httpsResponse);
        const hasSsl = this.checkSsl(httpsResponse);
        const responseBody = this.getResponseData(httpsResponse);
        if(!responseBody || !(responseBody instanceof String) || responseBody.trim() === '') {
            return {
                id: this.id,
                status: 0
            }
        }

        const headers = this.getHeaders(httpsResponse);
        const html = this.getHtml(responseBody)

        const title = this.getTitle(html);
        const description = this.getDescription(html);
        const keywords = this.getKeywords(html);

        let cms = this.guessCmsByHeaders(headers);
        if(cms === '') {
            cms = this.guessCms(html);
        }
        const innList = this.findInns(responseBody);
        const phoneList = this.findPhones(responseBody);
        const emailList = this.findEmails(responseBody);
        // const companyList = this.findCompanyName(responseBody);
        // const category = this.guessCategory(responseBody);

        // TODO: Отправлять поля company
        // const company = innList.length !== 0 ? await this.findFinanceInfo(innList) : {};
        const finances = [];

        return {
            'id': this.id,
            'status': status,
            'real_domain': realUrl,
            'has_https_redirect': hasHttpsRedirect,
            'has_ssl': hasSsl,
            'cms': cms,
            'title': title,
            'description': description,
            'keywords': keywords,
            'inn': innList,
            'phones': phoneList,
            'emails': emailList,
            // 'companies': companyList,
            // 'category': category,
            'finances': finances
        };
    }

    /**
     * Запрос по протоколу HTTP
     *
     * @param domain Домен
     * @returns {AxiosResponse|false}
     */
    async makeHttpRequest(domain)
    {
        return this.client.get('http://' + domain);
    }

    /**
     * Запрос по протоколу HTTPS
     *
     * @param domain Домен
     * @returns {AxiosResponse|false}
     */
    async makeHttpsRequest(domain) {
        return this.client.get('https://' + domain);
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

        const responseUrl = this.getRealUrl(response)

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
        const res = response.request ? response.request.res : {};
        return res ? res.responseUrl ?? '' : '';
    }

    hasCatalog() {
       // TODO: Implement
    }

    hasCart() {
        // TODO: Implement
    }

    /**
     * Получение тела ответа
     *
     * @param {AxiosResponse} response
     * @return {object}
     */
    getHeaders(response) {
        return response.headers ?? {};
    }

    /**
     * Получение тела ответа
     *
     * @param {AxiosResponse} response
     * @return {string}
     */
    getResponseData(response) {
        const body = response ? response.data : '';
        return body ? body ?? '' : '';
    }

    /**
     * Получение html из ответа
     *
     * @param {string} text Document text
     * @returns {HTMLElement}
     */
    getHtml(text) {
        return parse(text ?? '');
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
     *
     * @param headers
     */
    guessCmsByHeaders(headers) {
        const cmsExamples = {
            'ukit': {
                'x-cms': 'ukit'
            },
            'bitrix': {
                'x-powered-cms': 'bitrix site manager'
            },
            'nethouse': {
                'x-generator': 'nethouse'
            },
            'adobe muse': {
                'x-powered-by': 'plesklin'
            },
            'umi': {
                'x-generated-by': 'umi.cms'
            },
            'drupal': {
                'x-generator': 'drupal'
            },
            'okay': {
                'x-powered-cms': 'okaycms'
            },
            'phpshop': {
                'x-powered-by': 'phpshop'
            },
            'modx': {
                'x-powered-by': 'modx'
            },
            'magento': {
                'set-cookie': 'x-magento'
            }
        };

        for(const cms in cmsExamples) {
            const cmsHeaders = cmsExamples[cms];
            for(const cmsHeader in cmsHeaders) {
                const cmsHeaderValue = cmsHeaders[cmsHeader];
                for(const header in headers) {
                    const headerValue = headers[header.toLowerCase()].toString().toLowerCase();
                    if(headerValue.includes(cmsHeaderValue)) {
                        return cms;
                    }
                }
            }
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
            '<script src="https://static.tilda': 'tilda',
            'catalog/view/': 'opencart',
            'sources/ukit_font/': 'ukit',
            '/wa-data/': 'shop-script',
            '<meta name="modxru': 'modx',
            '<script src="/phpshop': 'phpshop',
            '<script type="text/javascript" src="/netcat': 'netcat',
            '<script type="text/x-magento-init': 'magento',
            'data-drupal-': 'drupal',
            'name="generator" content="Joomla': 'joomla',
            '/media/system': 'joomla',
            'var dle_admin': 'datalife engine',
            'UCOZ-JS': 'ucoz',
            'ucoz.net/': 'ucoz',
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
     * @param {string} text
     * @returns {string[]}
     */
    findInns(text) {
        const regexp = /\b\d{4}\d{6}\d{2}\b|\b\d{4}\d{5}\d{1}\b/gm;
        return text.match(regexp) ?? [];
    }

    /**
     * Валидация корректного ИНН
     *
     * @param {string} inn
     * @return {boolean}
     */
    isInnValid(inn) {
        if(inn.length !== 12 && inn.length !== 10) {
            return false;
        }

        const checkDigit = function(inn, coefficients) {
            let n = 0;
            for(const i in coefficients) {
                n += coefficients[i] * inn[i];
            }
            return parseInt(n % 11 % 10);
        };

        if(inn.length === 10) {
            const n10 = checkDigit(inn, [2, 4, 10, 3, 5, 9, 4, 6, 8]);
            if(n10 === parseInt(inn[9])) {
                return true;
            }
        }

        const n11 = checkDigit(inn, [7, 2, 4, 10, 3, 5, 9, 4, 6, 8]);
        const n12 = checkDigit(inn, [3, 7, 2, 4, 10, 3, 5, 9, 4, 6, 8]);

        return (n11 === parseInt(inn[10])) && (n12 === parseInt(inn[11]));
    }

    /**
     * Поиск номеров телефонов
     *
     * @param {string} text
     * @returns {string[]}
     */
    findPhones(text) {
        const regex = /\b(\+7|7|8)?[\s\-]?\(?[489][\d]{2}\)?[\s\-]?[0-9]{3}[\s\-]?[0-9]{2}[\s\-]?[0-9]{2}\b/gm;
        return text.match(regex) ?? [];
    }

    /**
     * Поиск электронных почт
     *
     * @param {string} text
     * @returns {string[]}
     */
    findEmails(text) {
        const regex = /[a-zA-Z0-9\.\-_]+@[a-zA-Z0-9_\-]+\.[a-zA-Z]+\.?[a-zA-Z]*\.?[a-zA-Z]*/gm;
        return text.match(regex) ?? [];
    }

    /**
     * Поиск названия компании
     * TODO: Улучшить
     *
     * @param {string} text
     * @returns {string[]}
     */
    findCompanyName(text) {
        const regex = /[ОПАЗНК]{2,3}\s+["'«]?[\w\dа-яА-Я\s]+["'»]?/gmu;
        return text.match(regex) ?? [];
    }

    /**
     * Получение категории сайта
     *
     * @param {string} text
     * @returns {string}
     */
    guessCategory(text) {
        // TODO: Implement
    }

    /**
     * Получение финансовых данных по ИНН
     *
     * @param {string[]} innList
     * @return {Promise[]}
     */
    async findFinanceInfo(innList) {
        const requests = [];
        for(const inn of innList) {
            requests.push(this.findFinanceInfoByInn(inn));
        }

        return Promise.all(requests);
    }

    /**
     * Получение финансовых данных по ИНН
     *
     * @param {string} inn
     * @return {Company}
     */
    async findFinanceInfoByInn(inn) {
        const companyParser = new Company(inn);
        companyParser.init();
        return companyParser;
    }

    /**
     * Сохранение данных о сайте в базу
     *
     * @returns {Promise<void>}
     */
    async saveToDb() {
        // TODO: Implement
    }

    /**
     * Получение информации о сайте с базы
     *
     * @returns {Promise<void>}
     */
    async getFromDb() {
        // TODO: Implement
    }


}

module.exports = Parser;
