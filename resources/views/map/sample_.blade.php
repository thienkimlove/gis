<!doctype html>
<html lang="en">
<head>
    <link rel="stylesheet" href="http://openlayers.org/en/v3.5.0/css/ol.css" type="text/css">
    <style>
        .map {
            height: 400px;
            width: 100%;
        }
    </style>
    <script src="http://openlayers.org/en/v3.5.0/build/ol.js" type="text/javascript"></script>
    <title>OpenLayers 3 example</title>
</head>
  <body>
    <h2>My Map</h2>
    <div id="map" class="map"></div>
    <script type="text/javascript">

        var image = new ol.style.Circle({
            radius: 5,
            fill: null,
            stroke: new ol.style.Stroke({color: 'red', width: 1})
        });

        var styles = {
            'Point': [new ol.style.Style({
                image: image
            })],
            'LineString': [new ol.style.Style({
                stroke: new ol.style.Stroke({
                    color: 'green',
                    width: 1
                })
            })],
            'MultiLineString': [new ol.style.Style({
                stroke: new ol.style.Stroke({
                    color: 'green',
                    width: 1
                })
            })],
            'MultiPoint': [new ol.style.Style({
                image: image
            })],
            'MultiPolygon': [new ol.style.Style({
                stroke: new ol.style.Stroke({
                    color: 'yellow',
                    width: 1
                }),
                fill: new ol.style.Fill({
                    color: 'rgba(255, 255, 0, 0.1)'
                })
            })],
            'Polygon': [new ol.style.Style({
                stroke: new ol.style.Stroke({
                    color: 'blue',
                    lineDash: [4],
                    width: 3
                }),
                fill: new ol.style.Fill({
                    color: 'rgba(0, 0, 255, 0.1)'
                })
            })],
            'GeometryCollection': [new ol.style.Style({
                stroke: new ol.style.Stroke({
                    color: 'magenta',
                    width: 2
                }),
                fill: new ol.style.Fill({
                    color: 'magenta'
                }),
                image: new ol.style.Circle({
                    radius: 10,
                    fill: null,
                    stroke: new ol.style.Stroke({
                        color: 'magenta'
                    })
                })
            })],
            'Circle': [new ol.style.Style({
                stroke: new ol.style.Stroke({
                    color: 'red',
                    width: 2
                }),
                fill: new ol.style.Fill({
                    color: 'rgba(255,0,0,0.2)'
                })
            })]
        };

        var styleFunction = function(feature, resolution) {
            return styles[feature.getGeometry().getType()];
        };

        var geojsonObject = {
            'type': 'FeatureCollection',
            'crs': {
                'type': 'name',
                'properties': {
                    'name': 'EPSG:3857'
                }
            },
            'features': [
                {
                    'type': 'Feature',
                    'geometry': {
                        'type': 'Point',
                        'coordinates': [0, 0]
                    }
                },
                {
                    'type': 'Feature',
                    'geometry': {
                        'type': 'LineString',
                        'coordinates': [[4e6, -2e6], [8e6, 2e6]]
                    }
                },
                {
                    'type': 'Feature',
                    'geometry': {
                        'type': 'LineString',
                        'coordinates': [[4e6, 2e6], [8e6, -2e6]]
                    }
                },
                {
                    'type': 'Feature',
                    'geometry': {
                        'type': 'Polygon',
                        'coordinates': [[[-5e6, -1e6], [-4e6, 1e6], [-3e6, -1e6]]]
                    }
                },
                {
                    'type': 'Feature',
                    'geometry': {
                        'type': 'MultiLineString',
                        'coordinates': [
                            [[-1e6, -7.5e5], [-1e6, 7.5e5]],
                            [[1e6, -7.5e5], [1e6, 7.5e5]],
                            [[-7.5e5, -1e6], [7.5e5, -1e6]],
                            [[-7.5e5, 1e6], [7.5e5, 1e6]]
                        ]
                    }
                },
                {
                    'type': 'Feature',
                    'geometry': {
                        'type': 'MultiPolygon',
                        'coordinates': [
                            [[[-5e6, 6e6], [-5e6, 8e6], [-3e6, 8e6], [-3e6, 6e6]]],
                            [[[-2e6, 6e6], [-2e6, 8e6], [0, 8e6], [0, 6e6]]],
                            [[[1e6, 6e6], [1e6, 8e6], [3e6, 8e6], [3e6, 6e6]]]
                        ]
                    }
                },
                {
                    'type': 'Feature',
                    'geometry': {
                        'type': 'GeometryCollection',
                        'geometries': [
                            {
                                'type': 'LineString',
                                'coordinates': [[-101422.01848094647,-96567.45208190096],[-101447.24781835916,-96664.86287584483],[-101262.62327931938,-96716.2023198559],[-101215.23302330916,-96547.86818131963],[-101308.03894132917,-96522.1984593141],[-101327.29123283332,-96586.37276432793],[-101422.01848094647,-96567.45208190096]]
                            },
                            {
                                'type': 'Point',
                                'coordinates': [4e6, -5e6]
                            },
                            {
                                'type': 'Polygon',
                                'coordinates': [[[1e6, -6e6], [2e6, -4e6], [3e6, -6e6]]]
                            }
                        ]
                    }
                }
            ]
        };

        var vectorSource = new ol.source.Vector({
            features: (new ol.format.GeoJSON()).readFeatures(geojsonObject)
        });

        vectorSource.addFeature(new ol.Feature(new ol.geom.Circle([5e6, 7e6], 1e6)));

        var vectorLayer = new ol.layer.Vector({
            source: vectorSource,
            style: styleFunction
        });

        var map = new ol.Map({
            layers: [
                new ol.layer.Tile({
                    source: new ol.source.OSM()
                }),
                vectorLayer
            ],
            target: 'map',
            controls: ol.control.defaults({
                attributionOptions: /** @type {olx.control.AttributionOptions} */ ({
                    collapsible: false
                })
            }),
            view: new ol.View({
                center: [0, 0],
                zoom: 2
            })
        });

    </script>
  </body>
</html>
