/**
 * csrdelft.nl javascript libje...
 */

import $ from 'jquery';

import initContext from './context';

function initGeolocation() { // eslint-disable-line

	let previousPos: Position;

    function positionSave(position : Position) {
        if (!previousPos || previousPos.coords == position.coords) {
            previousPos = position;
            $.post('/geolocation/save', {
                coords: position.coords,
                timestamp: Math.round(position.timestamp / 1000)
            });
        }
    }

    /**
     * @param {PositionError} error
     */
    function positionError(error : PositionError) {
        switch (error.code) {
            case error.PERMISSION_DENIED:
                break;
            case error.POSITION_UNAVAILABLE:
                break;
            case error.TIMEOUT:
                break;
        }
    }

    if (navigator.geolocation) {
        navigator.geolocation.watchPosition(positionSave, positionError);
    }
}

function initSluitMeldingen() {
    $('#melding').on('click', '.alert', function () {
        $(this).slideUp(400, function () {
            $(this).remove();
        });
    });
}

function zijbalkScrollFixed() {
    let elmnt = $('#zijbalk');
    if (!elmnt.length || !elmnt.hasClass('scroll-fixed')) {
        return;
    }

    if (elmnt.hasClass('desktop-only') && /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        elmnt.removeClass('desktop-only scroll-fixed dragobject dragvertical scroll-hover');
        return;
    }

    // adjust to container size
    $(window).on('resize', () => {
        elmnt.css('height', document.documentElement.clientHeight);
    });
    $(window).trigger('resize');

    // fix position on screen
    $(window).on('scroll', () => {
        elmnt.css({
            'top': $(window).scrollTop()!
        });
    });

    // set scroll position
    elmnt.scrollTop(Number(elmnt.attr('data-scrollfix')));

    // remember scroll position
    let trigger = false;

    function saveCoords() {
        $.post('/tools/dragobject', {
            id: 'zijbalk',
            coords: {
                top: elmnt.scrollTop(),
                left: elmnt.scrollLeft()
            }
        });
        trigger = false;
    }

    elmnt.on('scroll', function () {
        if (!trigger) {
            trigger = true;
            $(window).one('mouseup', saveCoords);
        }
    });

    // show-hide scrollbar
    if (elmnt.hasClass('scroll-hover')) {
        const showscroll = function () {
            if (elmnt.get(0).scrollHeight > elmnt.get(0).clientHeight) {
                elmnt.css({
                    'overflow-y': 'scroll'
                });
            }
        };
        const hidescroll = function () {
            elmnt.css({
                'overflow-y': 'hidden'
            });
        };
        elmnt.hover(showscroll, hidescroll);
    }
}

$(() => {
    zijbalkScrollFixed();
    initSluitMeldingen();
    initContext($('body'));

    let modal = $('#modal');
    if (modal.html() !== '') {
        modal.modal();
    }
    //initGeolocation();
});
