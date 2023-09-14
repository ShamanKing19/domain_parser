const {AxiosResponse, AxiosInstance} = require('axios');
const Logger = require('./logger');
const {Agent} = require('https');

class Request
{
    // userAgent = require('User-Agents');
    axios = require('axios');
    Functions = require('./functions');

    constructor() {
        this.defaultTimeout = 3000;
        this.functions = new this.Functions();
        this.logger = new Logger();
    }

    /**
     * GET запрос с параметрами и стандартным таймаутом
     *
     * ECONNABORTED - таймаут
     *
     * @param {string} url Ссылка
     * @param {object} config Конфиг
     *
     * @returns {AxiosResponse<any>}
     */
    async get(url, config = {}) {
        const client  = this.makeClientInstance();

        try {
            return await client.get(encodeURI(url), config);
        } catch (e) {
            if(e.response) {
                return await e.response;
            }

            return false;
        }
    }

    /**
     * POST запрос с параметрами и стандартным таймаутом
     *
     * @param {string} url Ссылка
     * @param {object} data Тело запроса
     * @param {object} config Конфиг
     *
     * @returns {AxiosResponse|false}
     */
    async post(url, data, config = {}) {
        const client  = this.makeClientInstance();

        try {
            return await client.post(encodeURI(url), data, config);
        } catch (e) {
            if(e && e.response) {
                return e.response;
            }

            return false;
        }
    }

    /**
     * Создание объекта клиента
     *
     * @param {object} config Конфигурация для запроса
     * @return {AxiosInstance}
     */
    makeClientInstance(config = {}) {
        if(!('timeout' in config)) {
            config['timeout'] = this.defaultTimeout;
        }

        config['httpsAgent'] = new Agent({
            rejectUnauthorized: false,
        });

        if(!('headers' in config)) {
            config['headers'] = {};
        }

        if(!('User-Agent' in config['headers'])) {
            config['headers'] = {
                'User-Agent': this.getUserAgent(),
            };
        }

        const defaultHeaders = {
            'Sec-Ch-Ua-Mobile': '?0',
            'Sec-Ch-Ua-Platform': "Windows",
            'Sec-Fetch-Dest': 'document',
            'Sec-Fetch-Mode': 'navigate',
            'Sec-Fetch-Site': 'none',
            'Sec-Fetch-User': '?1',
            'Upgrade-Insecure-Requests': 1,
            'Dnt': 1
        };

        for(const headerKey in defaultHeaders) {
            const headerValue = defaultHeaders[headerKey];
            if(!(headerKey in config['headers'])) {
                config['headers'][headerKey] = headerValue;
            }
        }

       return this.axios.create(config);
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

module.exports = Request;
