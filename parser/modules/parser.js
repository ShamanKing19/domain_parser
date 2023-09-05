class Parser
{
    /**
     * @param url Ссылка на сайт
     * @param id ID записи в базе данных
     */
    constructor(url, id = 0) {
        this.url = url;
        this.id = id;
    }

    getUrl() {
        return this.url;
    }

    getDomain() {
        return this.url.split('://').pop();
    }

    getId() {
        return this.id;
    }

    /**
     * Парсинг сайта
     *
     * @returns {Promise<void>}
     */
    async parse() {
        const domain = this.getDomain();

        const httpRequest = this.makeHttpRequest(domain);
        const httpsRequest = this.makeHttpsRequest(domain);

        const result = await Promise.all([httpRequest, httpsRequest]);
        const httpResponse = result[0];
        const httpsResponse = result[1];

        const status = this.getStatusCode(httpsResponse);
        const realUrl = this.getRealUrl(httpsResponse);
        const html = this.getHtml(httpsResponse);

        const title = this.getTitle(html);
        const description = this.getDescription(html);
        const keywords = this.getKeywords(html);

        const cms = this.guessCms(html);
        const innList = this.findInns(html);
        const phoneList = this.findPhones(html);
        const emailList = this.findEmails(html);
        const addressList = this.findAddresses(html);

        const category = this.guessCategory(html);

        const finances = innList.length !== 0 ? await this.findFinanceInfo(innList) : {};
    }

    async makeHttpRequest(domain) {

    }

    async makeHttpsRequest(domain) {

    }

    getStatusCode(response) {

    }

    getRealUrl(response) {

    }

    getHtml(response) {

    }

    getTitle(html) {

    }

    getDescription(html) {

    }

    getKeywords(html) {

    }

    guessCms(html) {

    }

    findInns(html) {

    }

    findPhones(html) {

    }

    findEmails(html) {

    }

    findAddresses(html) {

    }

    guessCategory(html) {

    }

    async findFinanceInfo(innList) {
        const requests = [];
        for(const inn of innList) {
            innList.push(this.findFinanceInfoByInn(inn));
        }

        return await Promise.all(requests);
    }

    async findFinanceInfoByInn(inn) {

    }

    /**
     * Сохранение данных о сайте в базу
     *
     * @returns {Promise<void>}
     */
    async saveToDb() {

    }

    /**
     * Получение информации о сайте с базы
     *
     * @returns {Promise<void>}
     */
    async getFromDb() {

    }


}

module.exports = Parser;
