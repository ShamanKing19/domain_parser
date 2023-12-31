const Parser = require('../modules/parser');
const Client = require("../modules/request");

const testUrl = 'zxc';

const testText = `
        <p>77 305 88 444</p>3906390130 <b>5635</b>652865<b>76</b>
        9500018475
        +8950001847, +89999999999, 40-50-34
        <a href="tel:79211455420">+7(921)145-54-20</a>
        <a href="tel:89211455420">8 921 145 54 20</a>
        7-921-145-54-20
        8 (921) 145-54-20

        u002F@-xxi.5662 /Rating@Mail.ru

        <a href="mailto:asd@asd.asd">123email.zxc123@kuku123.xyz</a>
       fake,email,@asd.asd fake-email@asdasd <b>a@a.a</b>aa@a.a

       hello@itfox-web.com

       "logo1@1x.png",
       logo1@2x.png",
       biotechnology-bann123er@1x.jpg",
       biotechnology-bann123er@2x.jpg,
       mrlk@1x.jpg",
       fnac_256@1x.png",
       fnac_256@2x.png,
       "kfu_vernadskogo@1x.png",

       name-_09@mail09-.ru

        <section id="requisites" class="requisites">
            <div class="requisites__wrapper">
                <div class="requisites__header">
                    <h2 class="requisites__title">Наши реквизиты</h2>
                    <button class="button requisites-button">скопировать реквизиты</button>
                </div>
                <table class="table js-table-requisites">
                    <tbody>
                        <tr id="bx_3995359085_32">
                            <td>Полное наименование</td>
                            <td>Общество с ограниченной ответственностью «САБЛАЙН СЕРВИС»</td>
                        </tr>
                        <tr id="bx_3995359085_33">
                            <td>Сокращенное наименование</td>
                            <td>ООО «САБЛАЙН СЕРВИС»</td>
                        </tr>
                        <tr id="bx_3995359085_34">
                            <td>Юридический адрес</td>
                            <td>109202, г. Москва, ул. Карачаровская 2-я, 1, стр. 1, офис 27</td>
                        </tr>
                        <tr id="bx_3995359085_35">
                            <td>Фактический адрес</td>
                            <td>143981, Московская область, г. Балашиха, микрорайон Кучино,ул. Центральная, 110</td>
                        </tr>
                        <tr id="bx_3995359085_36">
                            <td>ОГРН</td>
                            <td>1027739641412</td>
                        </tr>
                        <tr id="bx_3995359085_37">
                            <td>ИНН</td>
                            <td>7721218278</td>
                        </tr>
                        <tr id="bx_3995359085_38">
                            <td>КПП</td>
                            <td>772101001</td>
                        </tr>
                        <tr id="bx_3995359085_39">
                            <td>Банк</td>
                            <td>ПАО «Сбербанк»</td>
                        </tr>
                        <tr id="bx_3995359085_40">
                            <td>Расчетный счет</td>
                            <td>40702810038000027036</td>
                        </tr>
                        <tr id="bx_3995359085_41">
                            <td>Корреспондентский счет</td>
                            <td>30101810400000000225</td>
                        </tr>
                        <tr id="bx_3995359085_42">
                            <td>БИК</td>
                            <td>044525225</td>
                        </tr>
                        <tr id="bx_3995359085_43">
                            <td>Телефон, факс</td>
                            <td>+7 (495) 734-91-97</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    `;


test('inn validation', () => {
    const parser = new Parser(testUrl);

    const validInnList = ['3906390130', '7730588444', '9500018482', '9500018475', '9500018468', '972714924120', '971511159105'];
    const invalidInnList = ['3906390131', '3806390130', '38063901300', '123231123', 'adsqwwqdqdqdw', 8950001847];

    for(const inn of validInnList) {
        expect(parser.isInnValid(inn)).toBeTruthy();
    }

    for(const inn of invalidInnList) {
        expect(parser.isInnValid(inn)).toBeFalsy();
    }
});


test('find inns', () => {
    const parser = new Parser(testUrl);

    const innList = parser.findInns(testText);
    const correctResult = ['3906390130', '9500018475', '8950001847', '7721218278'];

    expect(innList).toStrictEqual(correctResult);
});

test('find phones', () => {
    const parser = new Parser(testUrl);
    const phoneList = parser.findPhones(testText);
    // TODO: 9500018475 - это ИНН, улучшить регулярку чтобы он не брался
    const correctResult = ['9500018475', '8950001847', '79211455420', '79211455420', '89211455420', '89211455420', '79211455420', '89211455420', '74957349197'];

    expect(phoneList).toStrictEqual(correctResult);
});

test('find emails', () => {
    const parser = new Parser(testUrl);

    const emailList = parser.findEmails(testText);
    const correctResult = ['Rating@Mail.ru', 'asd@asd.asd', '123email.zxc123@kuku123.xyz', 'hello@itfox-web.com'];

    expect(emailList).toStrictEqual(correctResult);
});

test('find company', () => {
    const parser = new Parser(testUrl);

    const emailList = parser.findCompanyName(testText);
    const correctResult = ['ООО «САБЛАЙН СЕРВИС»', 'ПАО «Сбербанк»'];

    expect(emailList).toStrictEqual(correctResult);
});

test('guess category', () => {
    const parser = new Parser(testUrl);
    // TODO: Implement
});
