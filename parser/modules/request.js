const {AxiosResponse, AxiosInstance} = require('axios');
const Logger = require("./logger");
const {Agent} = require("https");

class Request
{
    // userAgent = require('User-Agents');
    axios = require('axios');
    Functions = require('./functions');

    sleepTime = 3000;

    constructor() {
        this.functions = new this.Functions();
        this.logger = new Logger();
    }


    /**
     * Делает несколько попыток запроса по URL. Если ответ не будет получен возвращает false
     *
     * @param url       {string}        Строка запроса
     * @param config    {Object}        Кастомный конфиг для запроса
     * @param repeatTimes {int}         Количество повторений (При 10 работает хорошо)
     * @returns {Promise<AxiosResponse<any>|boolean>}
     */
    async tryGet(url, config = {}, repeatTimes = 1) {
        let response;

        for (let i = 0; i < repeatTimes; i++) {
            try {
                response = await this.get(url, config);
                return response;
            } catch (e) {
                await this.functions.sleep(this.sleepTime)
            }
        }

        return false;
    }

    /**
     * Делает несколько попыток запроса по URL. Если ответ не будет получен возвращает false
     *
     * @param url       {string}        Строка запроса
     * @param data      {Object}        Тело запроса
     * @param config    {Object}        Кастомный конфиг для запроса
     * @param repeatTimes {int}         Количество повторных запросов
     * @returns {Promise<AxiosResponse<any>|boolean>}
     */
    async tryPost(url, data, config = {}, repeatTimes = 100) {
        let response;

        for (let i = 0; i < repeatTimes; i++) {
            try {
                response = await this.post(url, data, config);
                return response;
            } catch (e) {
                await this.functions.sleep(this.sleepTime)
            }
        }

        return false;
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
            // if(!e) {
            //     return {
            //         status: 404,
            //         statusText: 'Not Found'
            //     }
            // }

            if(e.response) {
                await this.logger.log(`${e.code}: ${url}`, false, 'shit.txt')
                return e.response;
            }

            // if(e.code === 'ECONNABORTED') {
            //     return {
            //         status: 408,
            //         statusText: 'Timed Out'
            //     };
            // }

            // return {
            //     status: 0,
            //     statusText: 'Unhandled error'
            // };

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
            config['timeout'] = 3000;
        }
        if(!('headers' in config)) {
            config['headers'] = {};
        }

        if(!('User-Agent' in config['headers'])) {
            config['headers'] = {
                'User-Agent': this.getUserAgent(),
            };
        }

        Object.assign(config['headers'], {
            'Sec-Ch-Ua-Mobile': '?0',
            'Sec-Ch-Ua-Platform': "Windows",
            'Sec-Fetch-Dest': 'document',
            'Sec-Fetch-Mode': 'navigate',
            'Sec-Fetch-Site': 'none',
            'Sec-Fetch-User': '?1',
            'Upgrade-Insecure-Requests': 1,
            'Dnt': 1
        });

        config['httpsAgent'] = new Agent({
            rejectUnauthorized: false,
        });

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
