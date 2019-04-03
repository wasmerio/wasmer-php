document.addEventListener(
    'DOMContentLoaded',
    function () {
        var searchNode = document.getElementById('search');
        Elm.Search.embed(
            searchNode,
            {
                serializedSearchIndex: window.searchIndex || "",
                searchDatabase: window.searchMetadata || []
            }
        );
    }
);
