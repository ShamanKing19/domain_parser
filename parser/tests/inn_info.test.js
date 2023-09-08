const Parser = require('../modules/parser');
const Logger = require('../modules/logger');
const Company = require('../modules/company_parser');
const Functions = require('../modules/functions');

const logger = new Logger();
const functions = new Functions();

const testInn = '7730588444';
const company = new Company(testInn);
const companyData = functions.readJson( `${__dirname}/${testInn}.json`);
company.setFields(companyData);

// test('init company parser', async () => {
//     await parser.init();
//     expect(parser.getFields()).not.toStrictEqual({});
// });


// test('find finance info', async () => {
//     const parser = new Parser('zxc');
//
//     const notInfoInnList = ['231231231123'];
//     for(const inn of notInfoInnList) {
//         const financeInfo = await parser.findFinanceInfoByInn(inn);
//         expect(financeInfo).toBeInstanceOf(Company);
//         expect(financeInfo.getFields()).toStrictEqual({});
//     }
//
//     const innList = ['7730588444', '9500018482', '9500018475', '9500018468', '972714924120', '971511159105'];
//     for(const inn of innList) {
//         const company = await parser.findFinanceInfoByInn(inn);
//
//         expect(company).toBeInstanceOf(Company);
//         expect(company.getFields()).not.toStrictEqual({});
//     }
//
// });

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
    expect(company.getAuthorizedCapital()).toBe(12000);
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
