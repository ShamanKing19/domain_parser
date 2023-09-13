class Functions
{
    fs = require('fs');

    cleanPhone(string) {
        return string.replace(/[^0-9]+/gm, '');
    }

    readJson(filepath) {
        const data = this.fs.readFileSync(filepath);
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
