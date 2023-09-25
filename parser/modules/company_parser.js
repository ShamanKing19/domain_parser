const { AxiosResponse } = require('axios');
const { parse } = require('node-html-parser');
const Client = require('./request');
const Functions = require('./functions');

class Company
{
    financeInfoApiUrl = 'https://egrul.itsoft.ru';


    /**
     * @param {string} inn
     */
    constructor(inn) {
        this.inn = inn;
        this.client = new Client();
        this.functions = new Functions();
    }

    /**
     * Сбор информации о компании
     *
     * @returns Company
     */
    async init() {
        const response = await this.client.get(this.financeInfoApiUrl + `/${this.inn}.json`);
        return this.setFields(response ? response.data : {});
    }

    /**
     * ИНН
     *
     * @return {string}
     */
    getInn() {
        return this.inn;
    }

    /**
     * Все поля
     *
     * @return {object}
     */
    getFields() {
        return this.fields ?? {};
    }

    /**
     * Установка полей
     *
     * @param data
     * @returns Company
     */
    setFields(data) {
        this.fields = data;
        return this;
    }

    /**
     * Проверка: получены ли данные по компании
     *
     * @returns {boolean}
     */
    isParsed() {
        return this.getInn() !== '' && this.getFullName() !== '' && this.getRegion() !== '';
    }

    /**
     * Приведение к объекту для отправки
     *
     * @return {object}
     */
    toObject() {
        return this.functions.clean({
            'inn': this.getInn(),
            'name': this.getFullName(),
            'type': this.getType(),
            'region': this.getRegion(),
            'city': this.getCity(),
            'address': this.getAddress(),
            'post_index': this.getIndex(),
            'registration_date': this.getRegistrationDate(),
            'boss_name': this.getBossName(),
            'boss_post': this.getBossPosition(),
            'authorized_capital_type': this.getAuthorizedCapitalType(),
            'authorized_capital_amount': this.getAuthorizedCapitalAmount(),
            'registry_date': this.getRegistryDate(),
            'registry_category': this.getRegistryCategory(),
            'employees_count': this.getStaffCount(),
            'main_activity': this.getMainActivityName(),
            'last_finance_year': this.getLastFinanceYear(),
            'finances': this.getFinancialYearListFormatted()
        });
    }

    /**
     * "Сведения Юридического лица"
     *
     * @return {object}
     */
    getLegalEntityFields() {
        return this.getFields()['СвЮЛ'] ?? {};
    }

    /**
     * Полное название компании
     *
     * @return {string}
     */
    getFullName() {
        const fields = this.getLegalEntityFields()['СвНаимЮЛ'] ?? {};

        return fields['@attributes'] ? fields['@attributes']['НаимЮЛПолн'] ?? '' : '';
    }

    /**
     * Короткое название компании
     *
     * @return {string}
     */
    getShortName() {
        const fields = this.getLegalEntityFields()['СвНаимЮЛ'] ?? {};
        const shortFields = fields['СвНаимЮЛСокр'] ?? {};

        return shortFields['@attributes'] ? shortFields['@attributes']['НаимСокр'] ?? '' : '';
    }

    /**
     * Тип компании
     *
     * @return {string}
     */
    getType() {
        const fields = this.getLegalEntityFields()['@attributes'] ?? {};

        return fields['ПолнНаимОПФ'] ?? '';
    }

    /**
     * ОГРН
     */
    getOgrn() {
        const fields = this.getLegalEntityFields()['@attributes'] ?? {};

        return fields['ОГРН'] ?? '';
    }

    /**
     * Информация о местонахождении юридического лица
     *
     * @return {object}
     */
    getAddressFields() {
        return this.getLegalEntityFields()['СвАдресЮЛ'] ?? {};
    }

    /**
     * Название региона
     *
     * @return {string}
     */
    getRegion() {
        const data = this.getAddressFields();
        let fields = data['СвМНЮЛ'] ?? {};
        let regionName = fields['НаимРегион'] ?? '';

        if(regionName === '') {
            fields = data['АдресРФ'] ?? {};
            const regionInfo = fields['Регион'] ?? {};
            regionName = regionInfo['@attributes'] ? regionInfo['@attributes']['НаимРегион'] ?? '' : '';
        }

        if(regionName !== '') {
            const regionArray = regionName.split('.');
            regionName = regionArray[1] ?? regionArray[0] ?? '';
            return regionName.toUpperCase();
        }


        return '';
    }

    /**
     * Город
     *
     * @returns {string}
     */
    getCity() {
        const fields = this.getAddressFields()['АдресРФ'] ?? {};
        const city = fields['Город'] ?? {};
        const cityName =  city['@attributes'] ? city['@attributes']['НаимГород'] ?? '' : '';

        return cityName.toUpperCase();
    }

    /**
     * Улица
     *
     * @returns {string}
     */
    getStreet() {
        const fields = this.getAddressFields()['АдресРФ'] ?? {};
        const street = fields['Улица'] ?? '';
        const streetName =  street['@attributes'] ? street['@attributes']['НаимУлица'] ?? '' : '';
        const streetType = street['@attributes'] ? street['@attributes']['ТипУлица'] ?? '' : '';

        const array = [];
        if(streetType) {
            array.push(streetType);
        }
        if(streetName) {
            array.push(streetName);
        }

        return array.join(' ').toUpperCase();
    }

    /**
     * Здание, корпус, квартира
     *
     * @returns {string}
     */
    getBuilding() {
        const fields = this.getAddressFields()['АдресРФ'] ?? {};
        const address = fields['@attributes'] ?? {};
        if(address === {}) {
            return '';
        }

        const array = [];
        if(address['Дом']) {
            array.push(address['Дом']);
        }
        if(address['Корпус']) {
            array.push(address['Корпус']);
        }
        if(address['Кварт']) {
            array.push(address['Кварт']);
        }

        return array.join(', ').toUpperCase();
    }

    /**
     * Полный адрес
     *
     * @returns {string}
     */
    getAddress() {
        const region = this.getRegion();
        const city = this.getCity();
        const street = this.getStreet();
        const building = this.getBuilding();

        const array = [];
        if(region !== '') {
            array.push(region);
        }
        if(city !== '') {
            array.push('Г. ' + city);
        }
        if(street !== '') {
            array.push(street);
        }
        if(building !== '') {
            array.push(building);
        }

        return array.join(', ').toUpperCase();
    }

    /**
     * Почтовый индекс
     *
     * @returns {string}
     */
    getIndex() {
        const fields = this.getAddressFields()['АдресРФ'] ?? {};
        const address = fields['@attributes'] ?? {};
        if(address === {}) {
            return '';
        }

        return address['Индекс'] ?? '';
    }

    /**
     * Код региона
     *
     * @returns {string}
     */
    getRegionCode() {
        const fields = this.getAddressFields()['АдресРФ'] ?? {};
        const address = fields['@attributes'] ?? {};
        if(address === {}) {
            return '';
        }

        return address['КодРегион'] ?? '';
    }

    /**
     * Дата регистрации организации
     *
     * @returns {string}
     */
    getRegistrationDate() {
        const data = this.getLegalEntityFields()['СвОбрЮЛ'] ?? {};
        const fields = data['@attributes'] ?? {};

        return fields['ДатаОГРН'] ?? '';
    }

    /**
     * Сведения должностного физического лица
     *
     * @returns {object[]}
     */
    getIndividualEntityList() {
        const fields = this.getLegalEntityFields();
        return fields ? fields['СведДолжнФЛ'] ?? [] : [];
    }

    /**
     * Поиск директора
     *
     * @return {Object}
     */
    findBoss() {
        let staff = this.getIndividualEntityList();
        if(!Array.isArray(staff)) {
            staff = [staff];
        }

        for(const employee of staff) {
            const positionFields = employee['СвДолжн'] ?? {};
            const positionAttributes = positionFields['@attributes'] ?? {};
            let positionName = positionAttributes['НаимДолжн'] ?? '';
            positionName = positionName.toUpperCase();

            if(positionName.includes('ГЕНЕРАЛЬНЫЙ') || positionName === 'ДИРЕКТОР') {
                return employee;
            }
        }

        return staff.pop() ?? {};
    }

    /**
     * ФИО директора
     *
     * @return {string}
     */
    getBossName() {
        const fields = this.findBoss()['СвФЛ'] ?? {};
        const attributes = fields['@attributes'] ?? {};

        const array = [];
        if(attributes['Фамилия']) {
            array.push(attributes['Фамилия']);
        }
        if(attributes['Имя']) {
            array.push(attributes['Имя']);
        }
        if(attributes['Отчество']) {
            array.push(attributes['Отчество']);
        }

        return array.join(' ').toUpperCase();
    }

    /**
     * Должность директора
     *
     * @return {string}
     */
    getBossPosition() {
        const fields = this.findBoss()['СвДолжн'] ?? {};
        const attributes = fields['@attributes'] ?? {};

        return attributes['НаимДолжн'] ? attributes['НаимДолжн'].toUpperCase() : '';
    }

    /**
     * Уставный капитал
     *
     * @return {number}
     */
    getAuthorizedCapitalAmount() {
        const fields = this.getLegalEntityFields()['СвУстКап'] ?? {};
        const attributes = fields['@attributes'] ?? {};

        return attributes['СумКап'] ? Math.round(Number(attributes['СумКап'])) : 0;
    }

    /**
     * Тип уставного капитала
     *
     * @return {string}
     */
    getAuthorizedCapitalType() {
        return ''; // TODO: Implement
    }

    /**
     * Финансовая информация
     *
     * @return {object}
     */
    getFinancialInfoFields() {
        return this.getFields()['fin'] ?? {};
    }

    /**
     * ??? Дата внесения в реестр ???
     *
     * @return {string}
     */
    getRegistryDate() {
        const fields = this.getFinancialInfoFields()['msp'] ?? {};
        const attributes = fields['@attributes'] ?? {};

        return attributes['inc_date'] ?? '';
    }

    /**
     * Категория предприятия
     * 1 - СМСП микропредприятие
     *
     * @return {number}
     */
    getRegistryCategory() {
        const fields = this.getFinancialInfoFields()['msp'] ?? {};
        const attributes = fields['@attributes'] ?? {};

        return attributes['cat'] ? Number(attributes['cat']) :  0;
    }

    /**
     * Список годов с финансовой отчётностью
     *
     * [
     *     year1: {
     *         '@attributes': {...}
     *     },
     *     year2: {
     *         '@attributes': {...}
     *     }
     * ]
     *
     * @return {object[]}
     */
    getFinancialYearList() {
        const data = this.getFinancialInfoFields();
        const yearList = [];
        for(const key in data) {
            if(key[0] !== 'y') {
                continue;
            }

            const year = key.substring(1);
            yearList[year] = data[key];
        }

        return yearList;
    }

    /**
     * Список годов с финансовой отчётностью для отправки по api
     *
     * [
     *     {
     *         'year': 2021,
     *         'income': 100,
     *         'outcome': 1.3,
     *         'profit': 98.7
     *     },
     *     {
     *         'year': 2022,
     *         'income': 0,
     *         'outcome': 10,
     *         'profit': -10
     *     },
     *     ...
     * ]
     *
     * @return {object[]}
     */
    getFinancialYearListFormatted() {
        const data = this.getFinancialYearList();
        const result = [];
        for(const year in data) {
            const yearInfo = data[year];
            const attributes = yearInfo['@attributes'];
            if(!attributes) {
                continue;
            }

            const income = Number(attributes['income'] ?? 0);
            const outcome = Number(attributes['outcome'] ?? 0);
            result.push({
                'year': Number(year),
                'income': income,
                'outcome': outcome,
                'profit': income - outcome,
            });
        }

        return result;
    }

    /**
     * Последний год финансовой отчётности
     *
     * @return {number}
     */
    getLastFinanceYear() {
        const financeYears = this.getFinancialYearList();
        const years = Object.keys(financeYears).map((year) => Number(year));
        if(years.length === 0) {
            return 0;
        }

        return Math.max(...years);
    }

    /**
     * Поля последнего года финансовой отчётности
     *
     * 2028: {
     *     '@attributes': {
     *         "income": "52525000", // Прибиль
     *         "outcome": "50172000", // Убыль
     *         "tax_id_1": "658782",
     *         "tax_id_2": "0",
     *         "tax_id_3": "207458",
     *         "tax_id_5": "417040",
     *         "n": "14" // Количество сотрудников
     *     }
     * }
     *
     * @return {object}
     */
    getLastFinanceYearFields() {
        const financeYears = this.getFinancialYearList();
        const lastFinanceYear = this.getLastFinanceYear();

        return financeYears[lastFinanceYear];
    }

    /**
     * Количество сотрудников
     *
     * @return {number}
     */
    getStaffCount() {
        const fields = this.getLastFinanceYearFields() ?? {};
        const count = fields['@attributes'] ? fields['@attributes']['n'] ?? 0 : 0;

        return Number(count);
    }

    /**
     * Поля основного ОКВЭД'а
     *
     * @return {object}
     */
    getMainActivityFields() {
        const activityFields = this.getLegalEntityFields()['СвОКВЭД'] ?? {};

        return activityFields['СвОКВЭДОсн'] ?? {};
    }

    /**
     * Код основного ОКВЭД'а
     *
     * @return {string}
     */
    getMainActivityCode() {
        const activity = this.getMainActivityFields();

        return activity['@attributes'] ? activity['@attributes']['КодОКВЭД'] ?? '' : '';
    }

    /**
     * Название основного ОКВЭД'а
     *
     * @return {string}
     */
    getMainActivityName() {
        const activity = this.getMainActivityFields();

        return activity['@attributes'] ? activity['@attributes']['НаимОКВЭД'] ?? '' : '';
    }

    /**
     * Список дополнительных ОКВЭД'ов
     *
     * [
     *     {
     *         'name': 'Название ОКВЭД'а,
     *         'code': 'Код ОКВЭД'а'
     *     },
     *     ...
     * ]
     *
     * @returns {object[]}
     */
    getSideActivityList() {
        const activityFields = this.getLegalEntityFields()['СвОКВЭД'] ?? {};
        const sideActivityList = activityFields['СвОКВЭДДоп'] ?? [];
        const resultList = [];

        for(const activity of sideActivityList) {
            const fields = activity['@attributes'] ?? {};

            resultList.push({
                'name': fields['НаимОКВЭД'] ?? '',
                'code': fields['КодОКВЭД'] ?? ''
            });
        }

        return resultList;
    }



}

module.exports = Company;
