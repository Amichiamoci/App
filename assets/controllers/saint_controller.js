import { Controller } from '@hotwired/stimulus';
import $ from 'jquery';

export default class extends Controller {
    connect() {
        const $this = $(this.element);
        $.ajax('https://www.santodelgiorno.it/santi.json', {
            'method': 'GET',
            'cache': true,
            'success': function (data) {
                for (const saint of data)
                {
                    if (!Boolean(saint.default))
                    {
                        continue;
                    }
                    //console.log(`Oggi si ricorda ${saint.nome}, ${saint.tipologia}`);
                    
                    $this.children('span.saint-output').text(`${saint.nome}, ${saint.tipologia}`);
                    $this.removeClass('d-none');
                    return;
                }
            },
            'error': function() {
                console.error('Non è stato possibile caricare il santo del giorno');
            }
        });
    }
}
