const fs = require('fs');
const Client = require('./parser/modules/request');

(async () => {
    const fs = require('fs');
    let domains = fs.readFileSync('./domains.txt').toString().split('\n');
    // domains = domains.filter(function(item, pos){
    //     return domains.indexOf(item) === pos;
    // });

    const client = new Client();

    let domainNumber = domains.length;
    let sendData = [];
    for(const domain of domains) {
        sendData.push({'domain': domain.replace('\r', '')});

        domainNumber--;
        if(sendData.length === 10000) {
            const response = await client.post('https://domainsparse.dev.skillline.ru/api/domain/create-many', sendData);
            if(response.status !== 200) {
                console.log(response.data);
            }

            console.log(`Осталось ${domainNumber}...`);
            sendData = [];
        }
    }

    const response = await client.post('https://domainsparse.dev.skillline.ru/api/domain/create-many', sendData);
    console.log('ВСЁЁЁЁЁЁЁЁЁЁ');

})();
