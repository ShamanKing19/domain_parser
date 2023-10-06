const Parser = require('../modules/parser');

const cmsHeadersMap = {
    'ukit': ['https://007-agent.ru/'],
    'bitrix': ['https://unipump.ru', 'https://mnogomeb.ru', 'http://www.230vac.ru/'],
    'nethouse': ['http://23mayki.ru/', 'https://22altritual.ru/', 'https://24cpz.ru/', 'http://29dveri.ru/'],
    'adobe muse': ['http://24gadanie.ru/', 'https://1yog.ru/'],
    'umi': ['https://3-dental.ru/', 'https://5butterfly.ru/'],
    'drupal': ['https://24abs.ru/', 'https://74em.ru/', 'https://www.6-10kv.ru/'],
    'okay': ['https://agaxx.ru/'],
    'phpshop': ['https://42na.ru/', 'https://64-shop.ru/', 'http://a-ofis.ru/', 'http://accubat.ru/'],
    'modx': ['https://www.9474444.com/', 'https://agromolkom.ru/'],
    'magento': ['https://2009920.ru/'],
    'shopify': ['https://evengreener.com/'],
};

for(const cms in cmsHeadersMap) {
    const urlList = cmsHeadersMap[cms];
    for(const url of urlList) {
        test(`parser guess ${cms} by headers`, async () => {
            let parser = new Parser(url);
            await parser.init();

            if(!parser.client.isAvailable()) {
                parser = new Parser(url);
                await parser.init();
                await parser.checkRedirect();
            }

            const headers = parser.client.getHeaders();
            expect(parser.guessCmsByHeaders(headers)).toBe(cms);
        }, 10000);
    }
}


const cmsMap = {
    'bitrix': ['https://unipump.ru', 'https://mnogomeb.ru', 'http://www.230vac.ru/'],
    'wordpress': ['https://23uslugi-yurista.ru/', 'http://24dvs.ru/', 'https://russiandiamonds.ru/'],
    'tilda': ['https://240244.ru/', 'https://22bzem.ru/', 'http://22enota.ru/'],
    'joomla': ['http://21-raduga.ru/', 'https://21vek-city.ru/'],
    'opencart': ['https://23giga.ru/', 'http://2224284.ru/', 'https://245074.ru/'],
    'wix': ['https://www.78vo.ru/', 'https://www.abajursar.ru/'],
    'shop-script': ['https://24fusion.ru/', 'https://23akra.ru/', 'https://28bit.ru/'],
    'datalife engine': ['https://200volt.ru/', 'https://24inf.ru/', 'https://2391324.ru/', 'https://programmy-dlya-android.ru/', 'https://2hokage.ru/', 'https://stroim.5li.ru/'],
    'ucoz': ['http://23ru.ru/', 'http://41dou.ru/'],
    'nethouse': ['http://23mayki.ru/', 'https://22altritual.ru/', 'https://24cpz.ru/', 'http://29dveri.ru/'],
    'adobe muse': ['http://24gadanie.ru/', 'https://1yog.ru/', 'https://2leader.ru/'],
    'umi': ['http://33nasosa.ru/', 'https://3-dental.ru/', 'https://5butterfly.ru/'],
    'drupal': ['https://24abs.ru/', 'https://74em.ru/', 'https://www.6-10kv.ru/'],
    'okay': ['https://www.4lapy24.ru/', 'https://agaxx.ru/'],
    'phpshop': ['https://42na.ru/', 'https://64-shop.ru/', 'http://a-ofis.ru/', 'http://accubat.ru/'],
    'amiro': ['https://2580999.ru/', 'http://allagroup.ru/'],
    'netcat': ['https://4shiny.ru/', 'https://9213606.ru/', 'https://abraziv-chel.ru/', 'http://agava-shop.ru/', 'https://alcovrach.ru/'],
    'siteedit': ['http://a-svarka.ru/', 'https://agrcomp-rvd.ru/'],
    'modx': ['https://3kdveri.ru/', 'https://www.9474444.com/', 'https://agromolkom.ru/'],
    'magento': ['https://accuma.ru/', 'https://2009920.ru/'],
    'nuxt': ['https://skillline.ru/'],
    'next': ['https://bryantcodes.art/']
};

for(const cms in cmsMap) {
    const urlList = cmsMap[cms];
    for(const url of urlList) {
        test(`parser guess ${cms}`, async () => {
            const parser = new Parser(url);
            await parser.init();

            const html = parser.getHtml();
            expect(parser.guessCms(html)).toBe(cms);
        }, 10000);
    }
}
