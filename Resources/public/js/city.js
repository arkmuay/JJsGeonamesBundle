$.fn.localisationSearch = function() {

    var $this = this;

    /*
     $this.on('keypress', 'input, select', function(e) {
     if (e.keyCode == '13')
     e.preventDefault();
     }); */

    $this.find('.city').search({
        cache: true,
        minCharacters: 2,
        maxResults: 7,
        apiSettings: {
            cache: true,
            url: decodeURIComponent(Routing.generate('search_city', { "query": '{query}', "country": '{country}' })),
            // url: '/app_dev.php/suggest/city?search={query}&country={/country}',
            beforeSend: function(settings) {
                settings.urlData.country = $this.find('.country select').val();
                return settings;
            }
        },
        onSelect: function(result, response) {
            $('.city input[type="hidden"]').val(result.city);
            return true;
        },
        error : {
            noResults: 'Aucune ville trouvée.',
            serverError: 'Aucune ville trouvée au sein du pays sélectionné.'
        }
    });

    return this;
};


$(document).ready(function() {
    $('#localisation').localisationSearch();
});
