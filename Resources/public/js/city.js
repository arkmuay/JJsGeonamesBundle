$.fn.localisationSearch = function() {

    var $this = this;

    $this.find('.city').search({
        cache: false,
        minCharacters: 2,
        maxResults: 7,
        apiSettings: {
            cache: false,
            url: decodeURIComponent(Routing.generate('search_city', { "query": '{query}', "country": '{country}' })),
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
