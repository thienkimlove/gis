/**
 * Created by smagic39 on 7/16/15.
 */

$(document).ready(function () {
    $('#controlToolBar').multiselect();
});

/**
 * Call action
 */
var map, controls;
var SERVER_LIST = 'http://192.168.0.204/';
var SERVER_INTERSECT = window.base_url+'/hanlde-map';
var styleList = {
    red: '#ff0000'
};
var legend = $('#legend');
var indexListControl = [0, 1, 2, 4];
/**
 * Feature of Map Drawing
 */
var drawFeature = {};
function loadDefaultMap() {
    $('#map').html('');
    $('#map').show();
    $('#typeDraw').show();
    $('#legend').hide();

    var extent = [-103684.025507856, -148264.457938381, -18948.7372110224, -72812.9172731548];
    var projection = new ol.proj.Projection({
        code: 'EPSG:2455',
        // The extent is used to determine zoom level 0. Recommended values for a
        // projection's validity extent can be found at http://epsg.io/.
        //extent: [-2375745.92,-2845148.96,1036556.38,315856.59],
        extent: extent,
        // extent : [-96720.000000 -101470.000000 -96520.000000 -101210.000000],
        units: 'm'
    });
    ol.proj.addProjection(projection);



    var layers = [
        new ol.layer.Tile({
            source: new ol.source.TileWMS({
                url: SERVER_LIST + 'wms/allraster24550',
                params: {
                    'LAYERS': 'raster',
                    'VERSION': '1.1.1'
                }
            }),
            extent: extent
        })
    ];
    controls = [

        //new ol.control.Rotate({
        //    autoHide: false
        //}),
        new ol.control.ScaleLine(),
        new ol.control.Zoom(),
        //new ol.control.FullScreen(),
        /*new ol.control.OverviewMap({
         collapsed: false
         }),
         new ol.control.ZoomSlider(),
         new ol.control.ZoomToExtent(),
         new ol.control.Attribution(),
         */
        new ol.control.MousePosition({
            undefinedHTML: 'outside',
            projection: 'EPSG:3857',
            coordinateFormat: function (coordinate) {
                return ol.coordinate.format(coordinate, '{x}, {y}', 4);
            }
        })

    ];

    map = new ol.Map({
        controls: controls,
        target: 'map',
        view: new ol.View({
            projection: projection,
            center: ol.extent.getCenter(extent),
            //center : [143.062213357798, 43.0821154046763],
            zoom: 3
        })
    });
    removeControlCheckbox();
    return map;
}
function loadMap($id) {


    $('#map').html('');
    $('#map').show();
    $('#typeDraw').show();
    /**
     * Define a namespace for the application.
     */
    window.app = {};
    var app = window.app;



    /**
     * @constructor
     * @extends {ol.interaction.Pointer}
     */
    app.Drag = function() {

        ol.interaction.Pointer.call(this, {
            handleDownEvent: app.Drag.prototype.handleDownEvent,
            handleDragEvent: app.Drag.prototype.handleDragEvent,
            handleMoveEvent: app.Drag.prototype.handleMoveEvent,
            handleUpEvent: app.Drag.prototype.handleUpEvent
        });

        /**
         * @type {ol.Pixel}
         * @private
         */
        this.coordinate_ = null;

        /**
         * @type {string|undefined}
         * @private
         */
        this.cursor_ = 'pointer';

        /**
         * @type {ol.Feature}
         * @private
         */
        this.feature_ = null;

        /**
         * @type {string|undefined}
         * @private
         */
        this.previousCursor_ = undefined;

    };
    ol.inherits(app.Drag, ol.interaction.Pointer);


    /**
     * @param {ol.MapBrowserEvent} evt Map browser event.
     * @return {boolean} `true` to start the drag sequence.
     */
    app.Drag.prototype.handleDownEvent = function(evt) {
        var map = evt.map;

        var feature = map.forEachFeatureAtPixel(evt.pixel,
            function(feature, layer) {
                return feature;
            });

        if (feature) {
            this.coordinate_ = evt.coordinate;
            this.feature_ = feature;
        }

        return !!feature;
    };


    /**
     * @param {ol.MapBrowserEvent} evt Map browser event.
     */
    app.Drag.prototype.handleDragEvent = function(evt) {
        var map = evt.map;

        var feature = map.forEachFeatureAtPixel(evt.pixel,
            function(feature, layer) {
                return feature;
            });

        var deltaX = evt.coordinate[0] - this.coordinate_[0];
        var deltaY = evt.coordinate[1] - this.coordinate_[1];

        var geometry = /** @type {ol.geom.SimpleGeometry} */
            (this.feature_.getGeometry());
        geometry.translate(deltaX, deltaY);

        this.coordinate_[0] = evt.coordinate[0];
        this.coordinate_[1] = evt.coordinate[1];
    };


    /**
     * @param {ol.MapBrowserEvent} evt Event.
     */
    app.Drag.prototype.handleMoveEvent = function(evt) {
        if (this.cursor_) {
            var map = evt.map;
            var feature = map.forEachFeatureAtPixel(evt.pixel,
                function(feature, layer) {
                    return feature;
                });
            var element = evt.map.getTargetElement();
            if (feature) {
                if (element.style.cursor != this.cursor_) {
                    this.previousCursor_ = element.style.cursor;
                    element.style.cursor = this.cursor_;
                }
            } else if (this.previousCursor_ !== undefined) {
                element.style.cursor = this.previousCursor_;
                this.previousCursor_ = undefined;
            }
        }
    };


    /**
     * @param {ol.MapBrowserEvent} evt Map browser event.
     * @return {boolean} `false` to stop the drag sequence.
     */
    app.Drag.prototype.handleUpEvent = function(evt) {
        this.coordinate_ = null;
        this.feature_ = null;
        return false;
    };

    //draw
    var typeSelect = $('.type');
    var draw;
    $.post(
        window.base_url + '/api/show-map',
        {layerId: $id},
        function (data) {
            if (data.length === 0 || data === '') {
                alert('Data is empty');
                return;
            }
            data = $.parseJSON(data);
            $('#legend > img').attr('src', SERVER_LIST + 'wms/' + data.file_name + '?version=1.1.1&service=WMS&request=GetLegendGraphic&layer=sample&format=image/png');
            //draw
            var source = new ol.source.Vector({wrapX: false});

            var vectorSourceOSM2 = new ol.source.Vector({
                //url: 'http://gis.dev/hanlde-map',
                format: new ol.format.GeoJSON({
                    defaultDataProjection: 'EPSG:2455',
                    projection: 'EPSG:2455'

                })
            });

            var style = new ol.style.Style({
                fill: new ol.style.Fill({
                    color: '#000'
                }),
                stroke: new ol.style.Stroke({
                    width: 2,
                    color: 'rgba(255, 100, 50, 0.8)'
                }),
                image: new ol.style.Circle({
                    fill: new ol.style.Fill({
                        color: '#000'
                    }),
                    stroke: new ol.style.Stroke({
                        width: 1,
                        color: '#000'
                    }),
                    radius: 7
                })
            });

            var vectorLayerOSM2 = new ol.layer.Vector({
                source: vectorSourceOSM2,
                name: 'NAME 1',
                style: style
            });

            var polygonFeature = new ol.Feature(
                new ol.geom.Polygon([[[-101678, -96417], [-101000,-96463],
                    [-101000,-96463+50], [-101678, -96417+50], [-101678, -96417]]]));
            var vector1 =new ol.layer.Vector({
                    source: new ol.source.Vector({
                        features: [polygonFeature]
                    }),
                    style: new ol.style.Style({
                        image: new ol.style.Icon(/** @type {olx.style.IconOptions} */ ({
                            anchor: [0.5, 46],
                            anchorXUnits: 'fraction',
                            anchorYUnits: 'pixels',
                            opacity: 0.95,
                            src: 'data/icon.png'
                        })),
                        stroke: new ol.style.Stroke({
                            width: 3,
                            color: [255, 0, 0, 1]
                        }),
                        fill: new ol.style.Fill({
                            color: [0, 0, 255, 0.6]
                        })
                    })
                });
            var vector = new ol.layer.Vector({
                source: source,
                style: new ol.style.Style({
                    fill: new ol.style.Fill({
                        color: styleList.red
                    }),
                    stroke: new ol.style.Stroke({
                        color: styleList.red,
                        width: 2
                    }),
                    image: new ol.style.Circle({
                        radius: 7,
                        fill: new ol.style.Fill({
                            color: styleList.red
                        })
                    })
                })
            });

            ///Base map
            var layers = [
                new ol.layer.Tile({
                    source: new ol.source.TileWMS({
                        url: SERVER_LIST + 'wms/' + data.file_name,
                        params: {
                            'LAYERS': data.layers,
                            'VERSION': '1.1.1'
                        }
                    }),
                    showLegend: true
                }),
                vector1,
                vectorLayerOSM2,
                vector

            ];
            //Controlls
            controls = [

                //new ol.control.Rotate({
                //    autoHide: false
                //}),
                new ol.control.ScaleLine(),
                new ol.control.Zoom(),
                //new ol.control.FullScreen(),
                /*new ol.control.OverviewMap({
                 collapsed: false
                 }),
                 new ol.control.ZoomSlider(),
                 new ol.control.ZoomToExtent(),
                 new ol.control.Attribution(),
                 */
                new ol.control.MousePosition({
                    undefinedHTML: 'outside',
                    projection: 'EPSG:2455',
                    coordinateFormat: function (coordinate) {
                        return ol.coordinate.format(coordinate, '{x}, {y}', 4);
                    }
                })

            ];


            //scale
            removeControlCheckbox();

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

                    draw.on('drawend', function (evt) {
                        var parser = new ol.format.GeoJSON();
                        var features = source.getFeatures();
                        var featuresGeoJSON = parser.writeFeatures(features);
                        //Get Featuresformat
                        dataDraw = parser.writeFeatures(source.getFeatures());
                        dataDrawJSON = JSON.stringify(dataDraw, null, 4);
                        //respose intersection data
                        var tmpGeoObject = {};
                        $.post(
                            SERVER_INTERSECT,
                            {data: dataDraw},function (response) {
                            tmpGeoObject = $.parseJSON(response);
                            var vectorSource = new ol.source.Vector({
                                features: (new ol.format.GeoJSON()).readFeatures(tmpGeoObject)
                            });
                            var vectorLayer = new ol.layer.Vector({
                                source: vectorSource,
                                style: style
                            });
                            map.addLayer(vectorLayer);
                        });
                    }, this);
                    map.addInteraction(draw);
                }
            }

            map = new ol.Map({
                interactions: ol.interaction.defaults().extend([new app.Drag()]),
                controls: controls,
                maxExtent: data.extent,
                minScale: 250000,
                maxResolution: "auto",
                layers: layers,
                target: 'map',
                view: new ol.View({
                    projection: 'EPSG:2455',
                    //center: ol.extent.getCenter(data.extent),
                    center: data.central,
                    zoom: 7
                })
            });

            /**
             * Let user change the geometry type.
             * @param {Event} e Change event.
             */
            $('#clear').click(function () {

                vector.getSource().clear();

            });

            typeSelect.click(function (e) {
                map.removeInteraction(draw);
                addInteraction($(this).val());
            });
            addInteraction('None');
        });


}
function updateState() {
    var indexes = $('#controlToolBar').val();
    var stateResume = {};
    stateResume.last_active_layer_id = $('.data').jstree().get_selected();
    if (checkIndex("0", indexes)) {
        stateResume.is_invisible_scalebar = true;
        $('#state').attr('scalebar', '1');
    } else {
        stateResume.is_invisible_scalebar = false;
        $('#state').attr('scalebar', '');
    }
    if (checkIndex("1", indexes)) {
        stateResume.is_invisible_zoom_toolbar = true;
        $('#state').attr('zoom', '1');
    } else {
        stateResume.is_invisible_zoom_toolbar = false;
        $('#state').attr('zoom', '');
    }
    if (checkIndex("4", indexes)) {
        stateResume.is_invisible_legend = true;
        $('#state').attr('legend', '1');
    } else {
        stateResume.is_invisible_legend = false;
        $('#state').attr('legend', '');
    }
    $.ajax({
        url: window.base_url + '/user/updateStateOfUser',
        data: {
            'last_active_layer_id': stateResume.last_active_layer_id[0],
            'is_invisible_scalebar': stateResume.is_invisible_scalebar,
            'is_invisible_zoom_toolbar': stateResume.is_invisible_zoom_toolbar,
            'is_invisible_legend': stateResume.is_invisible_legend
        },
        type: 'post'
    });
}
function checkIndex(needle, haystack, argStrict) {

    var key = '',
        strict = !!argStrict;

    if (strict) {
        for (key in haystack) {
            if (haystack[key] === needle) {
                return true;
            }
        }
    } else {
        for (key in haystack) {
            if (haystack[key] == needle) {
                return true;
            }
        }
    }

    return false;
}
function removeControlCheckbox() {
    var scalebar = $('#state').attr('scalebar');
    if (scalebar) {
        $('#controlToolBar').multiselect('select', '0', true);
        map.removeControl(controls[0]);
    }
    var zoom = $('#state').attr('zoom');
    if (zoom) {
        $('#controlToolBar').multiselect('select', '1', true);
        map.removeControl(controls[1]);
    }
    var legend = $('#state').attr('legend');
    if (legend) {
        $('#controlToolBar').multiselect('select', '4', true);
        $('#legend').hide();
        map.removeControl(controls[4]);
    }
}
$(function () {
    //Control scalebar
    //selective

    $(document).on('change', '#controlToolBar', function () {
        var indexes = $(this).val();
        if (checkIndex("4", indexes)) {
            legend.hide();
        } else {
            legend.show();
        }
        var tmps = [];
        try {

            for (index in indexes) {
                tmps.push(parseInt(indexes[index]));
                map.removeControl(controls[parseInt(indexes[index])]);
            }
            $.each(indexListControl, function (i, val) {
                if (tmps.indexOf(val) == -1) {
                    map.addControl(controls[val]);
                }
            });
        } catch (err) {
            //console.log(err);
        }
        tmps = [];
        updateState();
    });
    //legen status
    $(document).on('click', '#showLegend', function () {

        if ($(this).is(':checked')) {
            legend.hide();
        } else {
            legend.show();
        }


    });
    function removeAllControl() {
        $.each([0, 1, 2, 4], function (i, val) {
            map.removeControl(controls[val]);
        });
    }

    function showAllControl() {
        $.each(indexListControl, function (i, val) {
            map.addControl(controls[val]);
        });
    }

    $('.show-nito-map').click(function () {
        $.fancybox([{
            href: window.base_url + '/map-prefix/nito',
            type: 'ajax',
            helpers: {
                overlay: {
                    closeClick: false
                }
            }
        }], {
            afterLoad: function (data) {
                try {
                    var json = $.parseJSON(data.content);
                    bootbox.dialog({
                        message: json.message,
                        title: Lang.get('common.error_title')
                    });
                    top.$.fancybox.close();
                    return false;
                } catch (err) {

                }
            }
        });
    });
    $(document).on('click', '.btn-create-map', function () {
        return $('.frm-validation-create-map').validationEngine('validate', {
            showOneMessage: true,
            onValidationComplete: function (form, status) {
                setTimeout(function () {
                    $('.frm-validation-create-map').validationEngine('hideAll');
                }, 4000);
                if (status === false)
                    return false;
                else {
                    var redirect_url = window.base_url + '/creating-map';
                    var map_id = $('#layer-id').val();
                    var crop_id = $('#crop-id').val();
                    var mode_type = $('#mode-type').val();

                    $.fancybox([{
                        href: redirect_url + '/' + map_id + '/' + crop_id + '/' + mode_type,
                        type: 'ajax',
                        helpers: {
                            overlay: {
                                closeClick: false
                            }
                        }
                    }], {
                        afterLoad: function (data) {
                            try {
                                var json = $.parseJSON(data.content);
                                bootbox.dialog({
                                    message: json.message,
                                    title: Lang.get('common.error_title')
                                });
                                top.$.fancybox.close();
                                return false;
                            } catch (err) {

                            }
                        }
                    });
                }
            }
        });
    });

});