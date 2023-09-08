class Logger
{
    fs = require('fs');
    logsDir = process.cwd() + '/parser/logs';
    logsPath = `${this.logsDir}/log.txt`;
    errorsPath = `${this.logsDir}/error.txt`;

    constructor() {
        if(!this.fs.existsSync(this.logsDir)) {
            this.fs.mkdirSync(this.logsDir);
        }
    }

    async log(message, consoleLog = false) {
        if(consoleLog) {
            console.log(message);
        }

        const now = new Date().toLocaleString();
        const formattedMessage = now + ': ' + message + '\n';
        await this.fs.appendFile(this.logsPath, formattedMessage, () => {});
    }

    async error(message, consoleLog = false) {
        if(consoleLog) {
            console.log(message);
        }

        const now = new Date().toLocaleString();
        const formattedMessage = now + ': ' + message + '\n';
        await this.fs.appendFile(this.errorsPath, formattedMessage, () => {});
    }

    json(filename, data) {
        this.fs.writeFileSync(`${this.logsDir}/${filename}.json`, JSON.stringify(data), 'utf-8');
    }

    logHtml(filename, data) {
        this.fs.writeFileSync(`${this.logsDir}/${filename}.html`, data);
    }
}

module.exports = Logger;
