<!DOCTYPE html>
<html>
    <head>
        <title>EPSG:4326 example</title>
        <script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ol3/3.6.0/ol.css" type="text/css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/ol3/3.6.0/ol.js"></script>
        <style>
            #map{
                height: 90%;
            }
        </style>
    </head>
    <body>
        <div class="container-fluid">

            <div class="row-fluid">
                <div class="col-lg-12 col-ms-12 col-md-12">
                    <div id="map" class="map "></div>
                    <form class="form-inline" id='typeDraw'>
                        <label class="radio-inline">
                            <input type="radio" class="btn btn-default type" name="type" value='Point'>Point
                        </label>
                        <label class="radio-inline">
                            <input type="radio" class="btn btn-default type" name="type" value='LineString'>LineString
                        </label>
                        <label class="radio-inline">
                            <input type="radio" class="btn btn-default type"  name="type" value='Polygon'>Polygon
                        </label>
                        <label class="radio-inline">
                            <input type="radio" class="btn btn-default type" name="type" value='Circle'>Circle
                        </label>
                        <label class="radio-inline">
                            <input type="radio" class="btn btn-default type" name="type" value='Square'>Square
                        </label>
                        <label class="radio-inline">
                            <input type="radio" class="btn btn-default type" name="type" value='Box'>Box
                        </label>
                        <label class="radio-inline">
                            <button type="button" class="btn btn-default" id="clear" >Clear</button>
                        </label>    
                    </form>
                </div>
            </div>

        </div>
        <script>
            var extent = [143.062147616611, 43.0804560792183, 143.064613784402, 43.082812715211];
            //    var bounds = new ol.Bounds(142.98333333333,42.661111111111,144.01666666667,43.338888888889);

            //draw
            var source = new ol.source.Vector({wrapX: false});

            var vector = new ol.layer.Vector({
                source: source,
                style: new ol.style.Style({
                    fill: new ol.style.Fill({
                        color: 'rgba(255, 255, 255, 0.2)'
                    }),
                    stroke: new ol.style.Stroke({
                        color: '#ffcc33',
                        width: 2
                    }),
                    image: new ol.style.Circle({
                        radius: 7,
                        fill: new ol.style.Fill({
                            color: '#ffcc33'
                        })
                    })
                })
            });
            ///Base map
            var layers = [
                new ol.layer.Tile({
                    source: new ol.source.TileWMS({
                        url: 'http://192.168.0.204/wms/gis20150715103805',
                        params: {
                            'LAYERS': 'raster,sample',
                            'VERSION': '1.1.1'
                        }
                    }),
                    showLegend:true
                }),
                vector
            ];
            //Controlls
            var controls = [
                new ol.control.Attribution(),
                new ol.control.MousePosition({
                    undefinedHTML: 'outside',
                    projection: 'EPSG:4326',
                    coordinateFormat: function (coordinate) {
                        return ol.coordinate.format(coordinate, '{x}, {y}', 4);
                    }
                }),
                new ol.control.OverviewMap({
                    collapsed: false
                }),
                new ol.control.Rotate({
                    autoHide: false
                }),
                new ol.control.ScaleLine(),
                new ol.control.Zoom(),
                new ol.control.ZoomSlider(),
                new ol.control.ZoomToExtent(),
                new ol.control.FullScreen()
            ];

            var map = new ol.Map({
                controls: controls,
                maxExtent: extent,
                minScale: 250000,
                maxResolution: "auto",
                layers: layers,
                target: 'map',
                view: new ol.View({
                    projection: 'EPSG:4326',
                    center: ol.extent.getCenter(extent),
                    zoom: 16
                })
            });

            //draw
            var typeSelect;
            $(function () {
                typeSelect = $('.type');
            });

            var draw; // global so we can remove it later
            function addInteraction(type) {
                var value = type;
                if (value !== 'None') {
                    var geometryFunction, maxPoints;
                    if (value === 'Square') {
                        value = 'Circle';
                        geometryFunction = ol.interaction.Draw.createRegularPolygon(4);
                    } else if (value === 'Box') {
                        value = 'LineString';
                        maxPoints = 2;
                        geometryFunction = function (coordinates, geometry) {
                            if (!geometry) {
                                geometry = new ol.geom.Polygon(null);
                            }
                            var start = coordinates[0];
                            var end = coordinates[1];
                            geometry.setCoordinates([
                                [start, [start[0], end[1]], end, [end[0], start[1]], start]
                            ]);
                            return geometry;
                        };
                    }
                    draw = new ol.interaction.Draw({
                        source: source,
                        type: /** @type {ol.geom.GeometryType} */ (value),
                        geometryFunction: geometryFunction,
                        maxPoints: maxPoints
                    });

                    draw.on('drawend',
                            function (evt) {
                                var parser = new ol.format.GeoJSON();
                                var features = source.getFeatures();
                                var featuresGeoJSON = parser.writeFeatures(features);
                                //Get Featuresformat   
                                data = parser.writeFeatures(source.getFeatures());
                                console.log(JSON.stringify(data, null, 4));

                            }, this);

                    map.addInteraction(draw);
                }
            }
            //clear draw 
            var clearButton = document.getElementById('clear');


            /**
             * Let user change the geometry type.
             * @param {Event} e Change event.
             */
            $(function () {
                $('#clear').click(function () {

                    vector.getSource().clear();

                });

                typeSelect.click(function (e) {
                    map.removeInteraction(draw);
                    addInteraction($(this).val());
                });
            });

            addInteraction('Point');

        </script>
    </body>
</html>