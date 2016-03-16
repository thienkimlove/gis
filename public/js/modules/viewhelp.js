// Parse query string to extract some parameters (it can fail for some input)
var query = document.location.href.replace(/^[^?]*(\?([^#]*))?(#.*)?/, '$2');
var queryParams = query ? JSON.parse('{' + query.split('&').map(function (a) {
    return a.split('=').map(decodeURIComponent).map(JSON.stringify).join(': ');
}).join(',') + '}') : {};
var fileName= $('#file').val().trim();
var url = queryParams.file || window.base_url +'/helps/'+ fileName;
var rootDiv = document.getElementById('holder');
//
// Fetch the PDF document from the URL using promises
//

//logging level = error (disable console.log warnings)

PDFJS.verbosity = 0;

PDFJS.getDocument(url).then(function(pdf) {
    var numPages = pdf.numPages;
    // Using promise to fetch the page

    // For testing only.
    var MAX_NUM_PAGES = 50;
    var ii = Math.min(MAX_NUM_PAGES, numPages);

    var promise = Promise.resolve();
    for (var i = 1; i <= ii; i++) {
        var anchor = document.createElement('a');
        anchor.setAttribute('name', 'page=' + i);
        anchor.setAttribute('title', 'Page ' + i);
        rootDiv.appendChild(anchor);

        // Using promise to fetch and render the next page
        promise = promise.then(function (pageNum, anchor) {
            return pdf.getPage(pageNum).then(function (page) {
                var desiredWidth = document.getElementById("holder").offsetWidth;
                var originViewport = page.getViewport(1);
                var viewport = page.getViewport(desiredWidth / originViewport.width);

                var container = document.createElement('div');
                container.id = 'pageContainer' + pageNum;
                container.className = 'pageContainer';
                container.style.width = viewport.width + 'px';
                container.style.height = viewport.height + 'px';
                anchor.appendChild(container);

                return page.getOperatorList().then(function (opList) {
                    var svgGfx = new PDFJS.SVGGraphics(page.commonObjs, page.objs);
                    return svgGfx.getSVG(opList, viewport).then(function (svg) {
                        container.appendChild(svg);
                    });
                });
            });
        }.bind(null, i, anchor));
    }
});
