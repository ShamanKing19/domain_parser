class Functions
{
    fs = require('fs');
    UserAgent = require('user-agents');
    axios = require('axios');

    sleepTime = 500;


    async writeJson(filepath, data) {
        const pathList = filepath.split('/');
        const filename = pathList.pop();
        const dirPath = pathList.join('/');
        if (!this.fs.existsSync(dirPath)) {
            await this.fs.mkdir(dirPath, {recursive: true}, () => {});
        }
        await this.fs.writeFile(filepath, JSON.stringify(data), () => {});
    }


    readJson(filepath) {
        const data = this.fs.readFileSync(filepath);
        console.log(data);
        return JSON.parse(data);
    }


    /**
     * Возвращает текущую дату в формате YYYY-MM-DD
     *
     * @return {string}
     */
    getCurrentDate() {
        return new Date().toISOString().split('T')[0];
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
                await this.sleep(this.sleepTime)
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
                await this.sleep(this.sleepTime)
            }
        }

        return false;
    }

    /**
     * GET запрос с параметрами и стандартным таймаутом в 5 секунд
     *
     * @param url       {string}    Строка запроса
     * @param config    {Object}    Кастомный конфиг
     * @returns {Promise<AxiosResponse<any>>}
     */
    async get(url, config = {}) {
        const instance = this.axios.create();
        if (!('timeout' in config)) {
            config['timeout'] = 3000;
        }
        if (!('headers' in config)) {
            config['headers'] = {
                'User-agent': this.getUserAgent(),
            };
        }
        return await instance.get(encodeURI(url), config);
    }

    /**
     * POST запрос с параметрами и стандартным таймаутом в 5 секунд
     *
     * @param url       {string}    Строка запроса
     * @param data      {{Object}}  Тело запроса
     * @param config    {{Object}}  Кастомный конфиг
     * @returns {Promise<AxiosResponse<any>>}
     */
    async post(url, data, config = {}) {
        const instance = this.axios.create();
        if (!('timeout' in config)) {
            config['timeout'] = 3000;
        }
        if (!('headers' in config)) {
            config['headers'] = {
                'User-agent': this.getUserAgent(),
            };
        }

        return await instance.post(encodeURI(url), data, config);
    }

    /**
     * Останавливает программу
     *
     * @param ms        {int}   Количество милисекунд
     * @returns void
     */
    async sleep(ms) {
        await new Promise(resolve => setTimeout(resolve, ms));
    }
}

module.exports = Functions;
