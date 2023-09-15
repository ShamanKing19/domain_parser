class Functions
{
    fs = require('fs');

    clean(obj) {
        for(const key in obj) {
            if(this.empty(obj[key])) {
                delete obj[key];
            }
        }

        return obj;
    }

    empty(value) {
        let undef;
        let key;
        let i;
        let len;
        const emptyValues = [undef, null, false, 0, '', '0'];
        for(i = 0, len = emptyValues.length; i < len; i++) {
            if(value === emptyValues[i]) {
                return true;
            }
        }
        if(typeof value === 'object') {
            for(key in value) {
                if(value.hasOwnProperty(key)) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

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
