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
