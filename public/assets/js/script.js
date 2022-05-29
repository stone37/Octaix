$(document).ready(function() {

    // Gestion des notification serveur
    flashes($('.notify'));

    // Bouton top
    $(window).scroll(function() {
        let scroll = $(window).scrollTop();
        let currScrollTop = $(this).scrollTop();

        if (scroll >= 200)
            $('#btn-smooth-scroll').removeClass('d-none').addClass('animated fadeInRight');
        else
            $('#btn-smooth-scroll').addClass('d-none').removeClass('animated fadeInRight');

        lastScrollTop = currScrollTop;
    });


    // Password show
    let $iconEye = $('.input-prefix.fa-eye');

    $iconEye.click(function () {
        let $this = $(this);

        if ($this.hasClass('fa-eye')) {
            $this.removeClass('fa-eye').addClass('fa-eye-slash view');

            $this.siblings('input').get(0).type = 'text';
        } else {
            $this.removeClass('fa-eye-slash view').addClass('fa-eye');

            $this.siblings('input').get(0).type = 'password';
        }
    });


    // Time js

    const terms = [
        {
            time: 45,
            divide: 60,
            text: "moins d'une minute"
        },
        {
            time: 90,
            divide: 60,
            text: 'environ une minute'
        },
        {
            time: 45 * 60,
            divide: 60,
            text: '%d minutes'
        },
        {
            time: 90 * 60,
            divide: 60 * 60,
            text: 'environ une heure'
        },
        {
            time: 24 * 60 * 60,
            divide: 60 * 60,
            text: '%d heures'
        },
        {
            time: 42 * 60 * 60,
            divide: 24 * 60 * 60,
            text: 'environ un jour'
        },
        {
            time: 30 * 24 * 60 * 60,
            divide: 24 * 60 * 60,
            text: '%d jours'
        },
        {
            time: 45 * 24 * 60 * 60,
            divide: 24 * 60 * 60 * 30,
            text: 'environ un mois'
        },
        {
            time: 365 * 24 * 60 * 60,
            divide: 24 * 60 * 60 * 30,
            text: '%d mois'
        },
        {
            time: 365 * 1.5 * 24 * 60 * 60,
            divide: 24 * 60 * 60 * 365,
            text: 'environ un an'
        },
        {
            time: Infinity,
            divide: 24 * 60 * 60 * 365,
            text: '%d ans'
        }
    ];

    let $dataTime = $('[data-time]');

    $dataTime.each(function (index, element) {
        const timestamp = parseInt(element.getAttribute('data-time'), 10) * 1000;
        const date = new Date(timestamp);

        updateText(date, element, terms);
    });

    $('.skin-light .navbar .btn.phone').hover(function() {
        let data = $(this).find('.fas');
        data.addClass('animated headShake infinite');

    }, function() {
        let data = $(this).find('.fas');
        data.removeClass('animated headShake infinite');
    });


    // Service
    let $serviceBulk = $('.app-service .service .card.card-image');

    $serviceBulk.hover(function() {
        let $this = $(this);

        $this.find('.second').hide()
        $this.find('.first').show()
    }, function() {
        let $this = $(this);

        $this.find('.second').show()
        $this.find('.first').hide()
    })

    let $inputRadio  = $('.app-radio-block .form-check-input[type="radio"]');
    let $radioBulk = $('.app-radio-block');

    $radioBulk.click(function() {
        let $this = $(this);

        let $input = $this.find('input');

        if (!$input.prop('checked')) {
            $input.prop('checked', true)
            $radioBulk.removeClass('choice');
            $this.addClass('choice')
        }

        /*$inputRadio.click(function () {
            let $this = $(this);

            if ($this.prop('checked')) {
                $input.prop('checked', false)
            } else {
                $input.prop('checked', true)
            }
        });*/
    });


});

function updateText(date, element, terms) {
    const seconds = (new Date().getTime() - date.getTime()) / 1000;
    let term = null;
    const prefix = element.getAttribute('prefix');

    for (term of terms) {
        if (Math.abs(seconds) < term.time) {
            break
        }
    }

    if (seconds >= 0) {
        element.innerHTML = `${prefix || 'Il y a'} ${term.text.replace('%d', Math.round(seconds / term.divide))}`
    } else {
        element.innerHTML = `${prefix || 'Dans'} ${term.text.replace('%d', Math.round(Math.abs(seconds) / term.divide))}`
    }
}

/**
 * Affiche des notifications
 *
 * @param titre
 * @param message
 * @param options
 * @param type
 */
function notification(titre, message, options, type) {
    if(typeof options == 'undefined') options = {};
    if(typeof type == 'undefined') type = "info";

    toastr[type](message, titre, options);
}

let options = {
    "closeButton": false, // true/false
    "debug": false, // true/false
    "newestOnTop": false, // true/false
    "progressBar": true, // true/false
    "positionClass": "toast-top-right", // toast-top-right / toast-top-left / toast-bottom-right / toast-bottom-left
    "preventDuplicates": false, //true/false
    "onclick": null,
    "showDuration": "300", // in milliseconds
    "hideDuration": "1000", // in milliseconds
    "timeOut": "8000", // in milliseconds
    "extendedTimeOut": "1000", // in milliseconds
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};

function simpleModals(element, route, elementRacine) {
    element.click(function (e) {
        e.preventDefault();

        let $id = $(this).attr('id'), $modal = '#confirm'+$id;

        $.ajax({
            url: Routing.generate(route, {id: $id}),
            type: 'GET',
            success: function(data) {
                $(elementRacine).html(data.html);
                $($modal).modal()
            }
        });
    });
}

function bulkModals(element, container, route, elementRacine) {
    element.click(function (e) {
        e.preventDefault();

        let ids = [];

        container.find('.list-checkbook').each(function () {
            if ($(this).prop('checked')) {
                ids.push($(this).val());
            }
        });

        if (ids.length) {
            let $modal = '#confirmMulti'+ids.length;

            $.ajax({
                url: Routing.generate(route),
                data: {'data': ids},
                type: 'GET',
                success: function(data) {
                    $(elementRacine).html(data.html);
                    $($modal).modal()
                }
            });
        }
    });
}

function flashes (selector) {
    selector.each(function (index, element) {
        if ($(element).html() !== undefined) {
            toastr[$(element).attr('app-data')]($(element).html());
        }
    })
}
