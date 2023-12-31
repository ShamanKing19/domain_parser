const {AxiosResponse} = require('axios');
const { parse } = require('node-html-parser');
const Functions = require('./functions');
const Client = require('./client');
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
        this.functions = new Functions();
        this.cmsBitrix = 'bitrix';
        this.emailBlackList = ['.jpg', 'jpeg', '.png', '.webp', '.css', '.js', 'beget.com', 'timeweb.ru', 'email@email.ru'];
    }

    /**
     * Получение ссылки на сайт
     *
     * @returns {string}
     */
    getUrl() {
        let url = this.url;
        if(!/https?:\/\//.test(url)) {
            url = 'https://' + url;
        }

        const urlLength = url.length;
        if(url[urlLength - 1] === '/') {
            url = url.substring(0, urlLength - 1);
        }

        return url;
    }

    /**
     * Получение домена (без протокола)
     *
     * @returns {string}
     */
    getDomain() {
        let url = this.url;
        const urlLength = url.length;
        if(url[urlLength - 1] === '/') {
            url = url.substring(0, urlLength - 1);
        }

        return url.split('://').pop();
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
     * Проверка: есть ли действительный ответ
     *
     * @return {boolean}
     */
    isAvailable() {
        return this.client.isAvailable();
    }

    /**
     * Https запрос к сайту
     *
     * @return {Promise<Parser>}
     */
    async init() {
        const domain = this.getDomain();
        this.client = await this.makeHttpsRequest(domain);
        this.status = this.client.getStatus();
        this.realUrl = this.client.getRealUrl();
        this.hasSsl = this.client.checkSsl();
        this.hasWwwRedirect = this.client.checkWwwRedirect();

        return this;
    }

    /**
     * Http запрос к сайту для проверки редиректа на https
     *
     * @return {Promise<Parser>}
     */
    async checkRedirect() {
        if(!this.isAvailable()) {
            return this;
        }

        const domain = this.getDomain();
        const client = await this.makeHttpRequest(domain);
        this.hasHttpsRedirect = client.checkHttpsRedirect()

        if(!this.client.isAvailable()) {
            this.client = client;
        }

        return this;
    }

    /**
     * Сбор информации с сайта
     *
     * @return {Parser}
     */
    parse() {
        if(!this.isAvailable()) {
            return this;
        }

        const headers = this.client.getHeaders();
        const html = this.getHtml();

        this.title = this.getTitle(html);
        this.description = this.getDescription(html);
        this.keywords = this.getKeywords(html);

        this.cms = this.guessCmsByHeaders(headers);
        if(this.cms === '') {
            this.cms = this.guessCms(html);
        }

        const responseBody = this.client.getBody();

        this.emailList = this.findEmails(responseBody); // Вот это говно работает 30 сек на 200 сайтах
        // this.emailList = this.findEmailsSimple(responseBody);
        this.phoneList = this.findPhones(responseBody);
        this.innList = this.findInns(responseBody);
        // this.companyList = this.findCompanyName(responseBody);
        // this.category = this.guessCategory(responseBody);

        return this;
    }

    /**
     * Проверка наличия каталога и корзины для битриксовый сайтов
     *
     * @return {Promise<Parser>}
     */
    async checkBitrixEcom() {
        if(!this.isAvailable()) {
            return this;
        }

        this.hasCatalog = false;
        this.hasCart = false;
        if(this.cms === this.cmsBitrix) {
            this.hasCatalog = await this.checkIfHasCatalog();
            this.hasCart = await this.checkIfHasCart();
        }

        return this;
    }

    /**
     * Сбор информации о компании
     *
     * @return {Promise<Parser>}
     */
    async collectCompanyInfo() {
        if(!this.innList || this.innList === []) {
            return this;
        }

        this.companies = await this.findFinanceInfo(this.innList);
        return this;
    }

    /**
     * Приведение к объекту для отправки
     *
     * @return {object}
     */
    toObject() {
        return {
            'id': this.id,
            'status': this.status ?? 0,
            'domain': this.getDomain(),
            'real_domain': this.realUrl ?? '',
            'has_https_redirect': this.hasHttpsRedirect ?? false,
            'has_ssl': this.hasSsl ?? false,
            'cms': this.cms ?? '',
            'has_catalog': this.hasCatalog ?? false,
            'has_basket': this.hasCart ?? false,
            'title': this.title ?? '',
            'description': this.description ?? '',
            'keywords': this.keywords ?? '',
            'phones': this.phoneList ?? [],
            'emails': this.emailList ?? [],
            'companies': this.companies ?? [],
            // 'type_id': 1,
            // 'auto_type_id': 1
        };
    }

    /**
     * Запрос по протоколу HTTP
     *
     * @param domain Домен
     * @returns {Client}
     **/
    async makeHttpRequest(domain)
    {
        const client = new Client('http://' + domain);
        await client.get();

        return client;
    }

    /**
     * Запрос по протоколу HTTPS
     *
     * @param domain Домен
     * @returns {Client}
     */
    async makeHttpsRequest(domain) {
        const client = new Client('https://' + domain);
        await client.get();

        return client;
    }

    /**
     * Проверка: есть ли каталог
     *
     * @returns Promise<boolean>
     */
    async checkIfHasCatalog() {
        const catalogUriList = ['/catalog', '/products', '/katalog', '/shop'];
        const requestList = [];
        for(const uri of catalogUriList) {
            const client = new Client(this.getUrl() + uri);
            requestList.push(client.head());
        }

        const clientList = await Promise.all(requestList);
        for(const client of clientList) {
            const status = client.getStatus();
            if(status >= 200 && status < 400) {
                return true;
            }
        }

        return false;
    }

    /**
     * Проверка: есть ли корзина
     *
     * @returns Promise<boolean>
     **/
    async checkIfHasCart() {
        const catalogUriList = ['/cart', '/basket', '/personal/basket', '/personal/cart', '/korzina'];
        const requestList = [];
        for(const uri of catalogUriList) {
            const client = new Client(this.getUrl() + uri);
            requestList.push(client.head());
        }

        const resultList = await Promise.all(requestList);
        for(const client of resultList) {
            const status = client.getStatus();
            if(status >= 200 && status < 400) {
                return true;
            }
        }

        return false;
    }

    /**
     * Получение html из ответа
     *
     * @returns {HTMLElement}
     */
    getHtml() {
        return parse(this.client.getBody() ?? '');
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
     * Определение cms по заголовкам
     *
     * @param headers
     */
    guessCmsByHeaders(headers) {
        const cmsExamples = {};
        cmsExamples[this.cmsBitrix] = {
            'x-powered-cms': 'bitrix site manager'
        };

        Object.assign(cmsExamples, {
            'wordpress': {
                'x-powered-by': 'wp engine'
            },
            'ukit': {
                'x-cms': 'ukit'
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
            },
            'shopify': {
                'powered-by': 'shopify'
            }
        });

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
            'src="/bitrix/': this.cmsBitrix,
            'href="/bitrix': this.cmsBitrix,
            'bitrix/templates/': this.cmsBitrix,
            'bitrix/cache/': this.cmsBitrix,
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
            '/_nuxt': 'nuxt',
            '<div id="__next"': 'next'
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
        let match = text.match(regex) ?? [];
        match = [...new Set(match)].map(item => this.functions.cleanPhone(item));

        return match.filter(item => {
            return !['79999999999', '89999999999', '9999999999'].includes(item);
        });
    }

    /**
     * Поиск электронных почт
     *
     * @param {string} text
     * @returns {string[]}
     */
    findEmails(text) {
        const regex = /\w[\w.-]*\w@[^-\W][a-zA-Z0-9-]*[^-\W]\.[a-zA-Z]+/gm;
        let match = text.match(regex) ?? [];
        match = [...new Set(match)];

        return this.functions.filterArray(match, this.emailBlackList);
    }

    /**
     * Поиск электронных почт (упрощённая регулярка)
     *
     * @param {string} text
     * @returns {string[]}
     */
    findEmailsSimple(text) {
        const regex = /[^@ \t\r\n]+@[^@ \t\r\n]+\.[^@ \t\r\n]+/gm;
        let match = text.match(regex) ?? [];
        match = [...new Set(match)];

        return this.functions.filterArray(match, this.emailBlackList);

    }

    /**
     * Поиск названия компании
     *
     * @param {string} text
     * @returns {string[]}
     */
    findCompanyName(text) {
        const regex = /[ОПАЗНК]{2,3}\s+["'«]?[\w\dа-яА-Я\s]+["'»]?/gmu;
        let match = text.match(regex) ?? [];
        return [...new Set(match)];
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
     * @return {Promise<[]>}
     */
    async findFinanceInfo(innList) {
        const requests = [];
        for(const inn of innList) {
            requests.push(this.findFinanceInfoByInn(inn));
        }

        let companies = await Promise.all(requests);
        companies = companies.filter(company => company.isParsed());

        return companies.map(company => company.toObject());
    }

    /**
     * Получение финансовых данных по ИНН
     *
     * @param {string} inn
     * @return {Company}
     */
    async findFinanceInfoByInn(inn) {
        const companyParser = new Company(inn);
        return companyParser.init();
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
