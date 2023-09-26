class Logger
{
    fs = require('fs');
    logsDir = process.cwd() + '/parser/logs';
    logsPath = `${this.logsDir}/log.txt`;
    errorsPath = `${this.logsDir}/error.txt`;

    async log(message, consoleLog = false, filepath = this.logsPath) {
        this.createLogsDir();
        const now = this.getCurrentTime();
        const formattedMessage = `[${now}]: ${message}\n`;
        if(consoleLog) {
            console.log(formattedMessage.replace('\n', ''));
        }

        if(filepath === this.logsPath) {
            this.fs.appendFile(this.logsPath, formattedMessage, () => {});
        } else {
            this.fs.appendFile(this.logsDir + '/' + filepath, formattedMessage, () => {});
        }
    }

    async error(message, consoleLog = false) {
        this.createLogsDir();
        const now = this.getCurrentTime();
        const formattedMessage = `[${now}]: ${message}\n`;
        if(consoleLog) {
            console.error(formattedMessage.replace('\n', ''));
        }

        this.fs.appendFile(this.errorsPath, formattedMessage, () => {});
    }

    async logJsonAsync(filename, data) {
        this.createLogsDir();
        this.fs.writeFile(`${this.logsDir}/${filename}.json`, JSON.stringify(data), 'utf-8', () => {});
    }

    logJson(filename, data) {
        this.createLogsDir();
        this.fs.writeFileSync(`${this.logsDir}/${filename}.json`, JSON.stringify(data), 'utf-8');
    }

    logHtml(filename, data) {
        this.createLogsDir();
        this.fs.writeFileSync(`${this.logsDir}/${filename}.html`, data);
    }

    createLogsDir() {
        if(!this.fs.existsSync(this.logsDir)) {
            this.fs.mkdirSync(this.logsDir);
        }
    }

    /**
     * Возвращает текущую дату в формате YYYY-MM-DD
     *
     * @return {string}
     */
    getCurrentTime() {
        const now = new Date();
        return now.toLocaleString();
    }
}

module.exports = Logger;
