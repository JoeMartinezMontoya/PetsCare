$(document).ready(function () {
    let addressInput = $('.address-input');
    let slug = $('#slug')[0].attributes[2].value;
    $('.pc-select').select2();

    const APPID = 'PngtAZGAQMdWWx2USwgc';
    const APIKEY = 'UhzVA8s3d298__corkYx1FFSANWssBNKTATgvgJMByY';

    if (addressInput !== null) {
        let options = {
            minimumInputLength: 3,
            language: {
                noResults: function () {
                    return "Aucun résultat";
                },
                searching: function () {
                    return "On cherche..."
                },
                inputTooShort: function () {
                    return "";
                }
            },
            ajax: {
                url: 'https://autocomplete.geocoder.ls.hereapi.com/6.2/suggest.json',
                delay: 250,
                dataType: 'json',
                data: function (params) {
                    return {
                        query: params.term,
                        app_id: APPID,
                        app_code: APIKEY,
                        apikey: APIKEY,
                        beginHighlight: '<b class="text-primary">',
                        endHighlight: '</b>',
                        country: 'FRA'
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data.suggestions, function (obj) {
                            return {
                                id: obj.locationId, text: obj.label.split(', ').reverse().join(', ')
                            };
                        })
                    };
                }
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        };

        /* On selection, hidden inputs catches lat and lng values */
        addressInput.select2(options).on('select2:select', function (e) {
            $.getJSON('https://geocoder.ls.hereapi.com/6.2/geocode.json', {
                app_id: APPID,
                app_code: APIKEY,
                apikey: APIKEY,
                locationId: e.params.data.id
            }).done(function (data) {
                let location = data.Response.View[0].Result[0].Location;
                $('#post_' + slug + '_lat').val(location.DisplayPosition.Latitude);
                $('#post_' + slug + '_lng').val(location.DisplayPosition.Longitude);
                $('#post_' + slug + '_town').val(location.Address.City);
            })
        });
    }
});