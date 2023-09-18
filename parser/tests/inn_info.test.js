const Parser = require('../modules/parser');
const Logger = require('../modules/logger');
const Company = require('../modules/company_parser');
const Functions = require('../modules/functions');

const logger = new Logger();
const functions = new Functions();

const testInn = '7730588444';
const company1 = new Company(testInn);

/**
 * Реальная инициализация парсера
 */
test('init company parser', async () => {
    await company1.init();
    expect(company1.getFields()).not.toStrictEqual({});
});

/**
 * ТЕСТОВЫЕ ДАННЫЕ
 */
const company = new Company(testInn);
const companyData = functions.readJson( `${__dirname}/${testInn}.json`);
company.setFields(companyData);

test('company fields set', () => {
    expect(company.getFields()).toStrictEqual(companyData);
});

test('get inn', () => {
    expect(company.getInn()).toBe(testInn);
});

test('get ogrn', () => {
    expect(company.getOgrn()).toBe('1087746982157');
});

test('read company data', () => {
    expect(company.getFields()).not.toStrictEqual({});
});

test('get full name', () => {
    expect(company.getFullName()).toBe('ОБЩЕСТВО С ОГРАНИЧЕННОЙ ОТВЕТСТВЕННОСТЬЮ \"ИТСОФТВАРЕ\"');
});

test('get short name', () => {
    expect(company.getShortName()).toBe('ООО \"ИТСОФТ\"');
});

test('get type', () => {
    expect(company.getType()).toBe('Общества с ограниченной ответственностью');
});

test('get segment', () => {
    // TODO: Определять сегмент компании по уровню доходности (в старом парсере есть инфа)
});

test('get address info', () => {
    expect(company.getAddressFields()).not.toStrictEqual({});
});

test('get region', () => {
    expect(company.getRegion()).toBe('МОСКВА');
});

test('get city', () => {
    expect(company.getCity()).toBe('МОСКВА');
});

test('get street', () => {
    expect(company.getStreet()).toBe('НАБ. МОСКВОРЕЦКАЯ');
});

test('get building', () => {
    expect(company.getBuilding()).toBe('Д. 7, СТР. 1, ПОМЕЩ. 44');
});

test('get index', () => {
    expect(company.getIndex()).toBe('109240');
});

test('get region code', () => {
    expect(company.getRegionCode()).toBe('77');
});

test('get full address', () => {
    expect(company.getAddress()).toBe('МОСКВА, Г. МОСКВА, НАБ. МОСКВОРЕЦКАЯ, Д. 7, СТР. 1, ПОМЕЩ. 44');
});

test('get registration date', () => {
    expect(company.getRegistrationDate()).toBe('2008-08-15');
});

test('get boss name', () => {
    expect(company.getBossName()).toBe('ТАРАСОВ ИГОРЬ АЛЕКСАНДРОВИЧ');
});

test('get boss position', () => {
    expect(company.getBossPosition()).toBe('ГЕНЕРАЛЬНЫЙ ДИРЕКТОР');
});

test('get yandex reviews', () => {
    // TODO: Implement
    // expect(company.getYandexReviewsUrl()).toBe('');
});

test('get google reviews', () => {
    // TODO: Implement
    // expect(company.getGoogleReviewsUrl()).toBe('');
});

test('get authorized capital', () => {
    expect(company.getAuthorizedCapitalAmount()).toBe(12000);
});

test('get registry date', () => {
    expect(company.getRegistryDate()).toBe('2016-08-01');
});

test('get registry category', () => {
    expect(company.getRegistryCategory()).toBe(1);
});

test('get last finance year', () => {
    expect(company.getLastFinanceYear()).toBe(2021);
});

test('get staff count', () => {
    expect(company.getStaffCount()).toBe(7);
});

test('get main activity code', () => {
    expect(company.getMainActivityCode()).toBe('62.01');
});

test('get main activity name', () => {
    expect(company.getMainActivityName()).toBe('Разработка компьютерного программного обеспечения');
});

test('get side activities', () => {
    expect(company.getSideActivityList()).toStrictEqual([
        {
            "code": "62.02.2",
            "name": "Деятельность по обследованию и экспертизе компьютерных систем",
        },
        {
            "code": "62.03.13",
            "name": "Деятельность по сопровождению компьютерных систем",
        },
        {
            "code": "62.09",
            "name": "Деятельность, связанная с использованием вычислительной техники и информационных технологий, прочая",
        },
        {
            "code": "63.11.1",
            "name": "Деятельность по созданию и использованию баз данных и информационных ресурсов",
        },
        {
            "code": "73.11",
            "name": "Деятельность рекламных агентств",
        },
        {
            "code": "74.20",
            "name": "Деятельность в области фотографии",
        },
        {
            "code": "74.30",
            "name": "Деятельность по письменному и устному переводу",
        },
        {
            "code": "82.92",
            "name": "Деятельность по упаковыванию товаров",
        },
        {
            "code": "85.42",
            "name": "Образование профессиональное дополнительное",
        }
    ]);
});

test('get finance info', async () => {
    expect(company.getFinancialYearListFormatted()).toStrictEqual([
        {year: 2014, income: 22972000, outcome: 17583000, profit: 5389000},
        {year: 2015, income: 28331000, outcome: 28445000, profit: -114000},
        {year: 2016, income: 25151000, outcome: 23307000, profit: 1844000},
        {year: 2017, income: 26620000, outcome: 24668000, profit: 1952000},
        {year: 2018, income: 24059000, outcome: 23750000, profit: 309000},
        {year: 2019, income: 34099000, outcome: 32818000, profit: 1281000},
        {year: 2020, income: 52525000, outcome: 50172000, profit: 2353000},
        {year: 2021, income: 88295000, outcome: 83168000, profit: 5127000}
    ]);
});
