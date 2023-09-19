const got = require('got');

/**
 * TODO: Покрыть всё тестами
 */
class Client
{
    constructor(url, data = {}, timeout = 3000) {
        this.url = url;
        this.timeout = timeout;
        this.data = data;
        this.response = {};
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
        if(Object.keys(this.data).length !== 0) {
            config.searchParams = this.data;
        }

        try {
            this.response = await got.get(this.url, config);
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
            'ERR_NON_2XX_3XX_RESPONSE': 403,
            'ECONNREFUSED': 404,
            'ENOTFOUND': 404,
            'ETIMEDOUT': 408,
            'EAI_AGAIN': 408,
            'Z_BUF_ERROR': 507,
            'Z_DATA_ERROR': 507,
            'EPROTO': 526,
            'EHOSTUNREACH': 0
        };

        if(e.code) {
            response.statusCode = errorsStatusMap[e.code];
        }

        if(e.name) {
            response.statusText = e.name;
        }

        if(!response.body) {
            response.body = '';
        }

        if(!response.statusCode) {
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
            https: {
                rejectUnauthorized: false
            },
            headers: {
                'User-Agent': this.getUserAgent(),
                'Sec-Ch-Ua-Mobile': '?0',
                'Sec-Ch-Ua-Platform': "Windows",
                'Sec-Fetch-Dest': 'document',
                'Sec-Fetch-Mode': 'navigate',
                'Sec-Fetch-Site': 'none',
                'Sec-Fetch-User': '?1',
                'Upgrade-Insecure-Requests': 1,
                'Dnt': 1
            }
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
}


module.exports = Client;
