const got = require('got');
const Logger = require('./logger');

/**
 * TODO: Покрыть всё тестами
 */
class Client
{
    constructor(url, data = {}, timeout = 3000, headers = {}) {
        this.url = url;
        this.data = data;
        this.timeout = timeout;
        this.customHeaders = headers;
        this.response = {};
        this.logger = new Logger();
    }

    isAvailable() {
        return this.getStatus() >= 200 && this.getStatus() < 400;
    }

    checkWwwRedirect() {
        return this.getRealUrl().includes('://www.');
    }

    checkHttpsRedirect() {
        return this.url.includes('http://') && this.getRealUrl().includes('https://');
    }

    checkSsl() {
        return this.getRealUrl().includes('https://');
    }

    getRealUrl() {
        return this.getResponse().url ?? '';
    }

    /**
     * Тело ответа в формате JSON
     *
     * @return {object}
     */
    getJson() {
        return JSON.parse(this.getBody());
    }

    /**
     * Тело ответа
     *
     * @return {string}
     */
    getBody() {
        return this.getResponse().body;
    }

    /**
     * Заголовки
     *
     * @return {Headers}
     */
    getHeaders() {
        return this.getResponse().headers;
    }

    /**
     * Текст ответа
     *
     * @return {string}
     */
    getStatusText() {
        return this.getResponse().statusMessage;
    }

    /**
     * Статус ответа
     *
     * @return {number}
     */
    getStatus() {
        return this.getResponse().statusCode;
    }

    /**
     * Ответ
     *
     * @return {Request}
     */
    getResponse() {
        return this.response ?? {};
    }

    /**
     * HEAD зарос
     *
     * @return {Promise<Client>}
     */
    async head() {
        const config = this.getConfig();
        if(Object.keys(this.data).length !== 0) {
            config.searchParams = this.data;
        }

        try {
            this.response = await got.head(this.url, config);
        } catch(e) {
            this.response = this.handleErrors(e);
        }

        return this;
    }

    /**
     * POST зарос
     *
     * @return {Promise<Client>}
     */
    async post() {
        const config = this.getConfig();
        if(Object.keys(this.data).length !== 0) {
            config.body = this.data;
        }

        try {
            this.response = await got.post(this.url, config);
        } catch(e) {
            this.response = this.handleErrors(e);
        }

        return this;
    }

    /**
     * GET зарос
     *
     * @return {Promise<Client>}
     */
    async get() {
        const config = this.getConfig();
        let params = '';
        if(Object.keys(this.data).length !== 0) {
            const sign = this.url.includes('?') ? '&' : '?';
            params += sign + this.serialize(this.data);
        }

        try {
            this.response = await got.get(this.url + params, config);
        } catch(e) {
            this.response = this.handleErrors(e);
        }

        return this;
    }

    /**
     * Обработка ошибок запроса
     *
     * @param {RequestError} e
     * @return {{}}
     */
    handleErrors(e) {
        const response = e.response ?? {};
        const errorsStatusMap = {
            'ERR_TOO_MANY_REDIRECTS': 310,
            'ERR_NON_2XX_3XX_RESPONSE': 403,
            'ECONNREFUSED': 404,
            'ECONNRESET': 404,
            'ENOTFOUND': 404,
            'ERR_INVALID_URL': 404,
            'ETIMEDOUT': 408,
            'EAI_AGAIN': 408,
            'Z_BUF_ERROR': 507,
            'Z_DATA_ERROR': 507,
            'EPROTO': 526,
            'EHOSTUNREACH': 0,
            'HPE_INVALID_HEADER_TOKEN': 0,
            'ENETUNREACH': 0,
        };

        if(e.code) {
            response.statusCode = errorsStatusMap[e.code];
        }

        // let errorMessage = e.name ?? e.code;
        // if(response.statusCode) {
        //     errorMessage += ' ' + response.statusCode;
        // }
        // this.logger.log(errorMessage, false, './../logs/response_errors.txt');

        if(e.name) {
            response.statusText = e.name;
        }

        if(!response.body) {
            response.body = '';
        }

        if(!response.statusCode && response.statusCode !== 0) {
            response.statusCode = 0;
            console.log(this.url, e);
        }

        return response;
    }

    /**
     * Стандартный конфиг
     *
     * @return {object}
     */
    getConfig() {
        return {
            timeout: {
                request: this.timeout,
            },
            retry: {
                limit: 0,
                maxRetryAfter: undefined,
            },
            https: {
                rejectUnauthorized: false
            },
            headers: Object.assign({
                'Connection': 'close',
                'User-Agent': this.getUserAgent(),
                'Sec-Ch-Ua-Mobile': '?0',
                'Sec-Ch-Ua-Platform': 'Windows',
                'Sec-Fetch-Dest': 'document',
                'Sec-Fetch-Mode': 'navigate',
                'Sec-Fetch-Site': 'none',
                'Sec-Fetch-User': '?1',
                'Upgrade-Insecure-Requests': 1,
                'Dnt': 1
            }, this.customHeaders)
        };
    }

    /**
     * Генерирует случайный User-Agent
     *
     * @returns {string}
     */
    getUserAgent() {
        return 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36';
    }

    serialize(obj, prefix) {
        let str = [],
            p;
        for (p in obj) {
            if (obj.hasOwnProperty(p)) {
                const k = prefix ? prefix + '[' + p + ']' : p,
                    v = obj[p];
                str.push((v !== null && typeof v === 'object') ?
                    this.serialize(v, k) :
                    encodeURIComponent(k) + '=' + encodeURIComponent(v));
            }
        }
        return str.join('&');
    }
}


module.exports = Client;
