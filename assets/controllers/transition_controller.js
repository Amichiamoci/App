import { Controller } from '@hotwired/stimulus';
import $ from 'jquery';

const CURIOSITIES = [
    // Sul cristianesimo in generale
    'La Bibbia non è un unico libro, bensì una raccolta di 73 diversi volumi',
    'La parola "vangelo" significa buona notizia',

    // Sui papi e lo sport
    'Papa Leone XIV è un grande appassionato di Tennis',
    'Papa Francesco definì lo Sport con 7 termini: Lealtà, Impegno, Sacrificio, Inclusione, Spirito di Gruppo, Ascesi, Riscatto',

    // Su Amichiamoci e Livorno
    'I calzini più originali sono quelli di Pietro',
    'Ogni edizione di Amichiamoci ha un diverso logo e slogan',
    'La scritta Amichiamoci colorata che vedi in quest\'App è una parte del logo 2014',
    'Amichiamoci anima da decenni il settembre della Diocesi di Livorno',
    '"Amicare" è un vero verbo della lingua italiana',
    'Negli anni, Amichiamoci ha sostenuto diversi enti benefici o religiosi, quali l\'UNHCR, le Figlie del Crocifisso o V.I.P. clown di corsia',
    'Il 27 gennaio 1742 un maremoto e un piccolo tsunami colpirono Livorno. ' + 
        'Soltanto con l\'intervento della Madonna di Montenero la città riuscì a salvarsi. ' + 
        'Da allora, i livornesi offrono cera per mantenere accesa la lampada votiva e non iniziano il Carnevale prima del 27 gennaio'
];

export default class extends Controller {
    #display_today_saint() {
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
                    
                    $this.children('span.curiosity-output').text(`Oggi la Chiesa ricorda ${saint.nome}, ${saint.tipologia}`);
                    $this.removeClass('d-none');
                    return;
                }
            },
            'error': function() {
                console.error('Non è stato possibile caricare il santo del giorno');
            }
        });
    }
    #display_curiosity() {
        const $this = $(this.element);
        const line = CURIOSITIES[Math.floor(Math.random() * CURIOSITIES.length)];
        $this.children('span.curiosity-output').text(line);
        $this.removeClass('d-none');
    }
    connect() {
        if (Math.random() >= 0.9)
        {
            this.#display_today_saint();
        } else {
            this.#display_curiosity();
        }
    }
}
