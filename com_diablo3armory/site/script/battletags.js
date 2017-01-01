jQuery(document).ready(function () {

    var b = Bnet.D3.Tooltips;
    b.registerDataOld = b.registerData;
    b.registerData = function (data) {
        var c = document.body.children, s = c[c.length - 1].src;
        data.params.key = s.substr(0, s.indexOf('?')).substr(s.lastIndexOf('/') + 1);
        this.registerDataOld(data);
    }

    jQuery('.herocell').hover(function () {
        jQuery(this).find('.herotooltip').show();
        // jQuery(this).find('.herotooltip').position({ at: 'bottom center', of: jQuery(this), my: 'top' });

        var bottom = jQuery(this).position().top + jQuery(this).outerHeight(true);
        var left = jQuery(this).position().left;

        var windowsize = jQuery(window).width();

        if (windowsize <= 430) {
            if (250 + left > windowsize) {
                left = windowsize - 250;
            }
        } else {
            if (596 + left > windowsize) {
                left = windowsize - 596;
            }
        }

        jQuery(this).find('.herotooltip').css({ top: bottom, left: left, position: 'absolute' });

        var loaded = jQuery(this).find('.hidden_detailsloaded').text();

        if (loaded == '0') {
            var tag = jQuery(this).find('.hidden_battletag').text();
            var heroId = jQuery(this).find('.hidden_heroid').text();

            updateHero(tag, heroId);

            jQuery(this).find('.hidden_detailsloaded').text('1');

            updateD3Tooltip();
        }
    });
    jQuery('.herocell').mouseleave(function () {
        jQuery(this).find('.herotooltip').hide();
    });
});

function getDivPosition(element) {
    var xPosition = 0;
    var yPosition = 0;

    while (element) {
        xPosition += (element.offsetLeft - element.scrollLeft + element.clientLeft);
        yPosition += (element.offsetTop - element.scrollTop + element.clientTop);
        element = element.offsetParent;
    }
    return { x: xPosition, y: yPosition };
}

function showBattleTagDetails(battletagId) {
    var element = "#battletag_details_" + battletagId;
    var plusImage = "#battletag_plus_" + battletagId;
    var minusImage = "#battletag_minus_" + battletagId;

    if (jQuery(element).is(":visible")) {
        jQuery(element).hide();
        jQuery(plusImage).show();
        jQuery(minusImage).hide();
    } else {
        jQuery(element).show();
        jQuery(plusImage).hide();
        jQuery(minusImage).show();
    }
}

function updateHero(id, hero) {
    jQuery.getJSON("index.php?option=com_diablo3armory&view=hero&format=raw&id=" + id + "&hero=" + hero, function (response) {
        jQuery('#herotooltip_' + hero).html(response);
    });
}

function updateD3Tooltip() {



}