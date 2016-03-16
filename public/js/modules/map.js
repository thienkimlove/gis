(function (module) {
    module.map = null; //store openLayer Map Object.
    module.defaultExtend = [15040864.8084697, 5036308.89314376, 16046240.559036, 5950679.406849];
    module.central = [];
    module.central25 = [15919771.3214436, 5334025.88236807];
    module.central50 = [15688687.2005229, 5236559.64516434];
    module.file_name50 = 'manytif2';
    module.file_name25 = 'manytif1';
    module.file_name = '';
    module.defaultEPSG = 3857;
    module.worldEPSG = 4326;
    module.mapElement =  $('#map');  //element where render map on.
    module.legendElement = null;   //element where have legend image.
    module.typeDrawElement = null; //element contain control draw flag (zoom, type, scale bar)
    module.projection = null; // projection of map.
    module.layers = [];   // store map layers
    module.storeFeatures = [];
    module.zoomTool = new ol.control.Zoom({delta: 0.6});
    module.controlToolBar = null;  // control tool bar.
    module.sketch = null;
    module.helpTooltipElement = null;
    module.draw = null;
    module.helpMsg = '';
    module.clearButton = null;
    module.drag = null;
    module.layerID = null;
    module.mapType = null;
    module.userId = null;
    module.minZoom = 12;
    module.maxZoom = 15;
    module.duration = 600;
    module.selectionPrevious = null;
    module.minSolution = Math.round(40075016.68557849 / 1854 / Math.pow(2, module.minZoom));
    module.minX = false;
    module.raster = 'raster25';
    module.startDraw = false;
    module.storeFinalLayer = {
        Point: [],
        LineString: []
    };

    /*  THOSE FUNCTIONS BELOW ONLY CALL INSIDE THIS CLASS         */
    /**
     * alway show raster
     */

    module.hideFertilityMap = function () {
        if (module.layers.length > 0 && module.map.getLayers().a.length > 1) {
            module.map.removeLayer(module.layers[1]);
        }
    };
    module.showFertilityMap = function () {
        if (module.layers.length > 0) {
            module.legendElement.show();
            module.map.addLayer(module.layers[1]);
        }
    };
    module.clearFeature = function () {
        for (var index in module.storeFeatures) {
            module.map.removeLayer(module.storeFeatures[index]);
        }
    };
    //Convert DD
    module._ParseDMS = function (input) {
        var parts = input.split(/[^\d\w\d.d]+/);
        return module._ConvertDMSToDD(parseInt(parts[0], 10), parseInt(parts[1], 10), parseFloat(parts[2]), parts[3]);
    };
    module._ConvertDMSToDD = function (degrees, minutes, seconds, direction) {
        var dd = degrees + minutes / 60 + seconds / (60 * 60);
        if (direction === "S" || direction === "W") {
            dd = dd * -1;
        }
        return dd;
    };
    module._latToDMS = function (lat) {
        var dms = module._toDMS(lat);
        if (lat > 0) {
            return "N" + dms;
        }
        else {
            return "S" + dms;
        }
    };
    module._lngToDMS = function (lng) {
        var dms = module._toDMS(lng);
        if (lng > 0) {
            return "E" + dms;
        }
        else {
            return "W" + dms;
        }
    };
    module._toDMS = function (latOrLng) {
        var d = parseInt(latOrLng, 10);
        var md = Math.abs(latOrLng - d) * 60;
        var m = Math.floor(md);
        var sd = (md - m) * 60;
        return Math.abs(d) + "\u00B0 " + m + "' " + module._roundNumber(sd, 4) + "\"";
    };
    module._roundNumber = function (num, dec) {
        "use strict";
        return Math.round(num * Math.pow(10, dec)) / Math.pow(10, dec);
    };
    module._wheel = function (event) {
        module.map.removeControl(new ol.control.Zoom());
        var delta = 0;
        if (!event) /* For IE. */
            event = window.event;
        if (event.wheelDelta) { /* IE/Opera. */
            delta = event.wheelDelta / 120;
        } else if (event.detail) { /** Mozilla case. */
            /** In Mozilla, sign of delta is different than in IE.
             * Also, delta is multiple of 3.
             */
            delta = -event.detail / 3;
        }
        if (delta > 0) {

            $('.ol-zoom-in').trigger('click');
            event.stopPropagation();
            if (event.preventDefault) event.preventDefault();
            event.stopPropagation();
            module.minZoom += 0.1;
            module.map.getView().setZoom(module.minZoom);
            return event.returnValue = false;


        } else {

            $('.ol-zoom-out').trigger('click');
            if (event.preventDefault) event.preventDefault();

            event.stopPropagation();
            module.minZoom -= 0.1;
            module.map.getView().setZoom(module.minZoom);

            return event.returnValue = false;
        }


    };
    //init map display.
    // we using gisObject.guess_prediction.vector to check if this is du doan thieu hut or normal map.
    module._init = function (config) {


        if (config == undefined) {
            config = {};
        }
        if (module.controlToolBar == null) {
            module.controlToolBar = (config.controlToolBar != undefined) ? config.controlToolBar : $('#controlToolBar');
            module.controlToolBar.multiselect();
        }


        module.mapElement = (config.mapElement != undefined) ? config.mapElement : $('#map');
        module.legendElement = (config.legendElement != undefined) ? config.legendElement : $('#legend img');
        module.typeDrawElement = (config.typeDrawElement != undefined) ? config.typeDrawElement : $('#typeDraw');
        module.typeDrawElement = (config.typeDrawElement != undefined) ? config.typeDrawElement : $('#typeDraw');
        module.clearButton = (config.clearButton != undefined) ? config.clearButton : $('#clear');
        module.mapElement.html('').show();
        module.typeDrawElement.show();

        //set map projection.
        if (module.projection == null) {
            module.projection = new ol.proj.Projection({
                code: 'EPSG:' + module.defaultEPSG,
                extent: module.defaultExtend,
                //units: 'm',
                getPointResolution: function (resolution, point) {
                    return resolution;
                }
            });
            ol.proj.addProjection(module.projection);
        }


        //clear layers array and interaction.
        module._clear();

        module.typeDrawElement.find('button[name="type"]').click(function () {
            module._addInteraction($(this).val());
            //solve problem when close the popup and click to input radio in case du doan thieu hut.
            if (gisObject.guess_prediction.vector) {
                module._removeDrawLayer($(this).val());
            }
        });
        module.clearButton.click(function () {
            module._clear();
            //case du doan thieu hut : we must remove all layers which already draw
            // clear guess_prediction info and also clear store steps.
            //and add interaction LineString.
            //this is we revert to first step
            if (gisObject.guess_prediction.vector) {
                module._clearMap();
            }
        });


        //in case du doan thieu hut.
        if (gisObject.guess_prediction.vector) {
            $("#main-tank, #sub-tank, #spread-width, #hojo-width").keyup(function () {
                module._clearMap();
            });
        }

        element = document.getElementById('map');
        if (element.addEventListener)
            element.addEventListener('DOMMouseScroll', module._wheel, false);
        element.onmousewheel = element.onmousewheel = module._wheel;

        mapPrediction = document.getElementById('mapPrediction');
        if($(mapPrediction).length > 0){
            if (mapPrediction.addEventListener)
                mapPrediction.addEventListener('DOMMouseScroll', module._wheel, false);
              mapPrediction.onmousewheel = mapPrediction.onmousewheel = module._wheel;
            $('#map').find('.ol-overlaycontainer-stopevent').remove();
            $('#map').find('.ol-overlaycontainer').remove();
        }


        $(document).on('keyup', function (event) {
            if (event.keyCode === 27) {
                module._removeCurrentInteraction();
                module._addInteraction();
            }
        });


    };

    //remove some layers base on click to radio input.
    // this function will call when we clear the map or click to each radio inputs.
    module._removeDrawLayer = function (type) {

        if (type == undefined) {
            module.storeFinalLayer = {
                Point: [],
                LineString: []
            };
        }

        if (type == 'Point') {
            for (var index in module.storeFinalLayer.Point) {
                module.map.removeLayer(module.storeFinalLayer.Point[index]);
            }

        }

        if (type == 'LineString') {
            gisObject.guess_prediction.set_point = false;
            for (var index in module.storeFinalLayer.LineString) {
                module.map.removeLayer(module.storeFinalLayer.LineString[index]);
            }
            for (var index in module.storeFinalLayer.Point) {
                module.map.removeLayer(module.storeFinalLayer.Point[index]);
            }
        }

        if (type == 'Drag') {
            gisObject.guess_prediction.set_point = false;
            for (var index in module.storeFinalLayer.Point) {
                module.map.removeLayer(module.storeFinalLayer.Point[index]);
            }
        }
    }

    //clear all layers, store data object in du doan thieu hut case.
    // this function will call in 2 places : when the clearButton click and when we init the map.
    module._clearMap = function () {
        gisObject.guess_prediction.polygons = [];
        gisObject.guess_prediction.line = [];
        gisObject.guess_prediction.point = [];
        gisObject.guess_prediction.set_point = false;
        module._removeDrawLayer();
        module.typeDrawElement.find('button[value="LineString"]').trigger('click');
    }

    module._createDraw = function (type) {
        if (type == null || type === undefined) {
            type = 'Polygon';
        }
        var geometryFunction, maxPoints;
        if (type === 'Square') {
            type = 'Circle';
            geometryFunction = ol.interaction.Draw.createRegularPolygon(4);
        } else if (type === 'Box') {
            type = 'LineString';
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
        } else if (type == 'Point') {
            gisObject.guess_prediction.set_point = true;
        } else if (type == 'Drag') {
            module.drag = new app.Drag;
            module.map.addInteraction(module.drag);
            return;
        }
        module.draw = new ol.interaction.Draw({
            source: new ol.source.Vector({wrapX: false}),
            type: /** @type {ol.geom.GeometryType} */ (type),
            style: new ol.style.Style({
                fill: new ol.style.Fill({
                    color: 'rgba(255, 255, 255, 0.2)'
                }),
                stroke: new ol.style.Stroke({
                    color: 'rgba(0, 0, 0, 0.5)',
                    lineDash: [10, 10],
                    width: 2
                }),
                image: new ol.style.Circle({
                    radius: 5,
                    stroke: new ol.style.Stroke({
                        color: 'rgba(0, 0, 0, 0.7)'
                    }),
                    fill: new ol.style.Fill({
                        color: 'rgba(255, 255, 255, 0.2)'
                    })
                })
            }),
            geometryFunction: geometryFunction,
            maxPoints: maxPoints
        });
        module.map.addInteraction(module.draw);
    };

    //work on both case : draw a box or polygon on fertility map
    // and prepare to merging color on fertilizer map
    module._processAfterDraw = function () {
        setTimeout(function () {
            //don't process any thing if user is in show all fertility mode
            if (gisObject.is_show_all_fertility) return;
            //modal for create map
            if (!gisObject.is_fertilizer) {
                if (
                    gisObject.crop_id == null
                    || gisObject.fertility_map_id == null
                    || gisObject.map_info_ids == null
                ) {
                    return false;
                }
                module._popupDialogFertility();
            } else {
                if (gisObject.map_info_ids == null || gisObject.map_info_ids.length == 0) {
                    return true;
                }
                changingcolor.openMergingColor();
            }

        }, 500);
        module.map.addInteraction(module.draw);
        $('#clear').show();
    };


    module._setLegend = function (filename, layer) {
        //fix for id
        var iframe = document.createElement('iframe');
        iframe.id = "IFRAMEID";
        iframe.style.display = 'none';
        document.body.appendChild(iframe);
        iframe.src = window.mapserver + filename + '?version=1.1.1&service=WMS&request=GetLegendGraphic&layer=' + layer + '&format=image/png';
        iframe.addEventListener("load", function () {

            module.legendElement
                .attr('src',
                window.mapserver + filename + '?version=1.1.1&service=WMS&request=GetLegendGraphic&layer=' + layer + '&format=image/png');
        });


    };

    module._checkIndex = function (needle, haystack, argStrict) {
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
    };

    module._popupDialogFertility = function () {
        if (gisObject.map_info_ids == null || gisObject.map_info_ids.length == 0) {
            return true;
        }

        bootbox.dialog({
            message: Lang.get('common.confirm_message'),
            title: Lang.get('common.info_title'),
            onEscape: function() { return false;},
            buttons: {
                success: {
                    label: Lang.get('common.yes_button'),
                    className: "btn-primary",
                    callback: function () {
                        creatingmap.openCreatingMap();
                    }
                },
                danger: {
                    label: Lang.get('common.no_button'),
                    className: "btn-danger",
                    callback: function () {
                        $('.bootbox.modal').remove();
                        if (module.selectionPrevious !== null && typeof  module.selectionPrevious === 'object') {
                            module._clear();
                            module._showLayerFromJson(module.selectionPrevious);
                        }

                    }
                },
                main: {
                    label: Lang.get('common.button_cancel'),
                    className: "btn-default",
                    callback: function () {
                        window.location.reload(true);
                    }
                }
            }
        });
    };

    module._createHelpTooltip = function () {

        if (module.helpTooltipElement && module.helpTooltipElement.parentNode) {
            module.helpTooltipElement.parentNode.removeChild(module.helpTooltipElement);
        }
        module.helpTooltipElement = document.createElement('div');
        module.helpTooltipElement.className = 'tooltip';
        module.helpTooltip = new ol.Overlay({
            element: module.helpTooltipElement,
            offset: [15, 0],
            positioning: 'center-left'
        });
        module.map.addOverlay(module.helpTooltip);
    };
    module.getRaster = function(data){
        return  new ol.layer.Tile({
            source: new ol.source.TileWMS({
                url: window.mapserver + data.file_name,
                params: {
                    'LAYERS': module.raster,
                    'VERSION': '1.1.1'
                }
            }),
            showLegend: true
        });


    };
    module._processMapRender = function (data, popUp) {
        if (popUp != undefined) {
            module._init(popUp);
        } else {
            module._init();
        }
        if (data.layers) {
            module._setLegend(data.file_name, data.layers.split(',')[0]);
            module.layers = [
                module.getRaster(data),
                new ol.layer.Tile({
                    source: new ol.source.TileWMS({
                        url: window.mapserver + data.file_name,
                        params: {
                            'LAYERS': data.layers,
                            'VERSION': '1.1.1'
                        }
                    }),
                    showLegend: true
                }),

            ]

        }
        module.map = new ol.Map({
            interactions: ol.interaction.defaults({mouseWheelZoom: false, doubleClickZoom: false}),
            controls: [
                new ol.control.ScaleLine({}),
                new ol.control.Zoom({delta: 1}),
                new ol.control.MousePosition({
                    undefinedHTML: '',
                    projection: 'EPSG:' + module.worldEPSG,
                    coordinateFormat: function (coordinate) {
                        var langLotCoordinate = ol.coordinate.format(coordinate, '{x}, {y}', 4);
                        var langLot = langLotCoordinate.split(',');
                        var lat = module._latToDMS(langLot[1]);
                        var long = module._lngToDMS(langLot[0]);
                        return lat + ' , ' + long;
                    }
                })
            ],
            minScale: 25000,
            maxResolution: 500000,
            layers: module.layers,
            target: module.mapElement.attr('id'),
            view: new ol.View({
                projection: module.projection,
                center: data.central,
                //maxZoom: module.maxZoom,
                //minZoom: (data.minZoom != undefined) ? data.minZoom : module.minZoom,
                zoom: module.minZoom
            })
        });
        if (module.minX) {
            module.map.getView().setResolution(module.minSolution);
        }


        if (data.selection != undefined) {
            var features = [];
            for (var index  in data.selection) {
                features.push($.parseJSON(data.selection[index]));
                gisObject.mode_selection_info_ids.push(index);
            }
            module.selectionPrevious = data.selection;
            module._showLayerFromJson(data.selection, {color: {Polygon: 'green'}});
        }
        module._updateState();


        module.map.on('pointermove', function (evt) {
            if (evt.dragging) {
                return;
            }

        });
        if (gisObject.guess_prediction.vector) {
            module._removeDrawLayer();
        }
        if (module.startDraw) {
            module._addInteraction();
        }
        module.map.getView().setZoom(module.minZoom);
        module.minX = false;
        //module.map.renderSync();
        if (data.is_invisible_layer) {
            module.hideFertilityMap();
            module.legendElement.hide();
            return true;
        }
        module.legendElement.show();
        if (data.bounding != undefined) {
            module.map.getView().fit(data.bounding, module.map.getSize());
        }
        return true;

    };

    module._showLayerFromJson = function (object, config) {

        if (config == undefined) {
            config = {};
        }

        if (config.color == undefined) {
            config.color = {};
        }

        if (config.fill == undefined) {
            config.fill = {};
        }

        if (config.clear !== undefined) {
            module._clear();
        }

        var image = new ol.style.Circle({
            radius: 5,
            fill: null,
            stroke: new ol.style.Stroke({color: 'red', width: 1})
        });

        var styles = {
            'Point': [new ol.style.Style({
                image: (config.final != undefined) ? new ol.style.Icon(/** @type {olx.style.IconOptions} */ ({
                    src: window.base_url + '/images/checked.png',
                    rotateWithView: false
                })) : image
            })],
            'LineString': [new ol.style.Style({
                stroke: new ol.style.Stroke({
                    color: (config.color.LineString != undefined) ? config.color.LineString : 'green',
                    width: 1
                })
            })],
            'MultiLineString': [new ol.style.Style({
                stroke: new ol.style.Stroke({
                    color: (config.color.MultiLineString != undefined) ? config.color.MultiLineString : 'green',
                    width: 1
                })
            })],
            'MultiPoint': [new ol.style.Style({
                image: image
            })],
            'MultiPolygon': [new ol.style.Style({
                stroke: new ol.style.Stroke({
                    color: (config.color.MultiPolygon != undefined) ? config.color.MultiPolygon : 'yellow',
                    width: 1
                }),
                fill: (config.fill.MultiPolygon != undefined || config.fill.MultiPolygon == null) ? config.fill.MultiPolygon : new ol.style.Fill({
                    color: 'rgba(255, 255, 0, 0.1)'
                })
            })],
            'Polygon': [new ol.style.Style({
                stroke: new ol.style.Stroke({
                    color: (config.color.Polygon != undefined) ? config.color.Polygon : 'blue',
                    lineDash: [4],
                    width: 3
                }),
                fill: (config.fill.Polygon != undefined || config.fill.Polygon == null) ? config.fill.Polygon : new ol.style.Fill({
                    color: 'rgba(0, 0, 255, 0.1)'
                })
            })],
            'GeometryCollection': [new ol.style.Style({
                stroke: new ol.style.Stroke({
                    color: (config.color.GeometryCollection != undefined) ? config.color.GeometryCollection : 'magenta',
                    width: 2
                }),
                fill: (config.fill.GeometryCollection != undefined || config.fill.GeometryCollection == null) ? config.fill.GeometryCollection : new ol.style.Fill({
                    color: 'magenta'
                }),
                image: new ol.style.Circle({
                    radius: 10,
                    fill: null,
                    stroke: new ol.style.Stroke({
                        color: 'magenta'
                    })
                })
            })]
        };

        var styleFunction = function (feature) {
            return styles[feature.getGeometry().getType()];
        };

        var features = [];

        var iconFeatures = [];

        var iconStartFeatures = [];

        //we have three types of display a geometries.

        //1. directly display feature - with config.directly = true,
        // when we have only one geometry object  { type : xx, coordinates : [] }

        //2. break geometry to display separately - with config.separate = true
        //where we have a object with contain multi geometries object but we dont want to display as GeometryCollection

        //3. display object with contain multi geometries object like GeometryCollection
        if (config.directly != undefined) {
            features.push({
                'type': 'Feature',
                'geometry': object
            });
        } else if (config.separate != undefined) {
            for (var index  in object) {
                features.push({
                    'type': 'Feature',
                    'geometry': $.parseJSON(object[index])
                });
            }
        } else if (config.addition != undefined) {
            for (var index  in object) {
                features.push({
                    'type': 'Feature',
                    'geometry': object[index].geo
                });
            }
        } else if (config.final != undefined) {
            for (var index  in object) {
                features.push({
                    'type': 'Feature',
                    'geometry': $.parseJSON(object[index])
                });
            }
        } else if (config.arrow != undefined) {

            for (var index  in object) {
                var obj = object[index];
                features.push({
                    'type': 'Feature',
                    'geometry': obj.geo
                });

                var lineString = new ol.geom.LineString(obj.geo.coordinates);
                var start = lineString.getFirstCoordinate();
                var end = lineString.getLastCoordinate();
                if ((obj.endpoint[0] == start[0]) && (obj.endpoint[1] == start[1])) {
                    start = lineString.getLastCoordinate();
                    end = lineString.getFirstCoordinate();
                }

                iconStartFeatures.push(new ol.Feature({
                    geometry: new ol.geom.Point(start),
                    num: parseInt(index) + 1
                }));

                var dx = end[0] - start[0];
                var dy = end[1] - start[1];
                var rotation = Math.atan2(dy, dx);

                iconFeatures.push(new ol.Feature({
                    geometry: new ol.geom.Point(end),
                    rotation: rotation
                }));
            }

        } else {
            var ars = [];
            for (var index  in object) {
                ars.push($.parseJSON(object[index]));
            }
            features.push({
                'type': 'Feature',
                'geometry': {
                    'type': 'GeometryCollection',
                    'geometries': ars
                }
            });
        }
        var geoJsonObject = {
            'type': 'FeatureCollection',
            'crs': {
                'type': 'name',
                'properties': {
                    'name': 'EPSG:' + module.defaultEPSG
                }
            },
            'features': features
        };

        var vectorLayer = new ol.layer.Vector({
            source: new ol.source.Vector({
                features: (new ol.format.GeoJSON()).readFeatures(geoJsonObject)
            }),
            style: styleFunction
        });

        if (config.outside == undefined) {
            module.storeFeatures.push(vectorLayer);
            module.map.addLayer(vectorLayer);

            if (config.point_clear != undefined) {
                module.storeFinalLayer.Point.push(vectorLayer);
            }

            if (config.line_clear != undefined) {
                module.storeFinalLayer.LineString.push(vectorLayer);
            }

            if (iconFeatures.length > 0) {

                var styleStartFunction = function (feature) {
                    return [new ol.style.Style({
                        image: new ol.style.Circle({
                            radius: 12,
                            stroke: new ol.style.Stroke({color: 'black', width: 1})
                        }),
                        text: new ol.style.Text({
                            textAlign: 'enter',
                            textBaseline: 'middle',
                            font: 'normal 10px Arial',
                            text: feature.get('num'),
                            fill: new ol.style.Fill({color: 'black'}),
                            stroke: new ol.style.Stroke({width: 1}),
                            offsetX: 0,
                            offsetY: 0
                        })
                    })]
                };

                var styleIconFunction = function (feature) {
                    return [new ol.style.Style({
                        image: new ol.style.Icon(/** @type {olx.style.IconOptions} */ ({
                            src: window.base_url + '/images/arrow.png',
                            anchor: [0.75, 0.5],
                            rotateWithView: false,
                            rotation: -feature.get('rotation')
                        }))
                    })];
                };

                var markLayer = new ol.layer.Vector({
                    source: new ol.source.Vector({
                        features: iconFeatures
                    }),
                    style: styleIconFunction
                });

                var textLayer = new ol.layer.Vector({
                    source: new ol.source.Vector({
                        features: iconStartFeatures
                    }),
                    style: styleStartFunction
                });

                module.storeFeatures.push(markLayer);
                module.map.addLayer(markLayer);

                module.storeFeatures.push(textLayer);
                module.map.addLayer(textLayer);

                module.storeFinalLayer.Point.push(textLayer);
                module.storeFinalLayer.Point.push(markLayer);
            }
        } else {
            return vectorLayer;
        }

    };

    module._removeCurrentInteraction = function () {
        if (module.map != undefined) {
            module.map.removeInteraction(module.draw);
            module.map.removeInteraction(module.drag);
        }
    }

    module._clear = function () {
        //remove all layers from map.
        module.clearFeature();
        //reset layers arrays.
        module.storeFeatures = [];
    };


    module._selectionProcess = function (geoJson) {
        if (gisObject.guess_prediction.vector) {

            gisObject.guess_prediction.line = {
                'type': 'LineString',
                'coordinates': geoJson
            };

            var fertilizerWidth = $("#spread-width").val();
            var fieldWidth = $("#hojo-width").val();

        }
        if (gisObject.guess_prediction.vector || gisObject.is_fertilizer) {
            //when start processing for: prediction or change&merge color of fertilizer
            //we need to clear previous drawing
            module.clearFeature();
        }
        //store polygon selection area when creating new fertilizer map
        $("#polygonSelectionAreaForFertilizerCreation").val(geoJson);

        $.post(window.base_url + '/selection', {
            polygon: geoJson,
            layer_id: gisObject.layer_id,
            map_id: gisObject.fertility_map_id,
            is_fertilizer: gisObject.is_fertilizer,
            mode_selection_info_ids: gisObject.mode_selection_info_ids,
            vector: (gisObject.guess_prediction.vector) ? gisObject.guess_prediction.vector : false,
            fertilizer_width: fertilizerWidth,
            field_width: fieldWidth
        }).then(function (data) {
            if (data && data != '') {
                //we clear previous drawing when user selection right area
                module.clearFeature();
            } else {
                //show message box to end-user and also return
                //otherwise the gisObject.mode_selection_info_ids will be clear
                fancyAlert(Lang.get('common.common_selection_right_area'), window.info_title);
                gisMap._addInteraction('Polygon');
                return;
            }
            if (data.message != undefined) {
                return fancyAlert(data.message, window.info_title);
            }
            if (data == '') {
                fancyAlert(Lang.get('common.common_selection_right_area'), window.info_title);
                gisMap._addInteraction('Polygon');
                return;
            }
            //clear selection info if current mode is not for creating fertilizer
            if (gisObject.is_fertilizer) {
                gisObject.mode_selection_info_ids = [];
            }
            if (data.vector != undefined) {
                module.sketch = null;
                module._showLayerFromJson(data.lines, {fill: {GeometryCollection: null}, line_clear: true});

                var ars = [];
                for (var index  in data.lines) {
                    ars.push($.parseJSON(data.lines[index]));
                }
                gisObject.guess_prediction.polygons = {
                    'type': 'GeometryCollection',
                    'geometries': ars
                };
                gisObject.guess_prediction.fertilizer_map_id = data.fertilizer_map_id;
            } else {
                gisObject.map_info_ids = [];
                for (var index  in data) {
                    gisObject.map_info_ids.push(index);
                }
                module.sketch = null;
                module._showLayerFromJson(data);
                module._processAfterDraw();

            }
        });
    };

    //start draw on current layers.
    module._addInteraction = function (type) {
        //default for draw
        //remove current interaction if have.
        module._removeCurrentInteraction();
        //create a draw type : box or polygon.
        module._createDraw(type);

        module.draw.on('drawstart',
            function (evt) {
                module.sketch = evt.feature;
            }, this);
        module.draw.on('drawend',
            function (evt) {
                var geoJson = module.sketch.getGeometry().getCoordinates();
                module._removeCurrentInteraction();
                if (gisObject.guess_prediction.set_point) {
                    var point = {
                        'type': 'Point',
                        'coordinates': geoJson
                    };
                    module._showLayerFromJson(point, {directly: true, point_clear: true});
                    gisObject.guess_prediction.point = point;
                    gisMap.generate();
                } else {
                    //donot clear feature here, it'll be called at _selectionProcess function
                    //module.clearFeature();
                    module._selectionProcess(geoJson);
                }
            }, this);
    };
    module._updateState = function () {
        if (gisObject.config.state.last_active_layer_id != undefined || gisObject.layer_id != undefined) {
            $.post(window.base_url + '/user/updateStateOfUser', {
                'last_active_layer_id': gisObject.layer_id
            });
        }
    };

    /* MAIN FUNCTION CALL FROM OUTSIDE CLASS */

    //load default map.
    module.defaultMap = function (layerId) {


        module._init();
        var data = {file_name: module.file_name25, layers: module.raster, central: module.central25};
        var postData = {
            layerDefault: layerId ? layerId : null
        };

        $.post(window.base_url + '/api/show-map', postData, function (response) {

            if (response) {
                response = $.parseJSON(response);
            } else {
                return false;
            }
            module.raster = response[0].raster;

            data.file_name = response[0].file_name;
            data.layers = response[0].raster;
            if (data.file_name == module.file_name50) {
                data.central = module.central50;
            }
            module.raster = response[0].raster;
            module._processMapRender(data);
            if (module.layerID) {
                module.loadMap(module.layerID);
            }

        });

        module.minX = true;
        $('#clear').hide();

        return true;

    };
    module._visible_fertilizer_function = function () {

        if (gisObject.is_fertilizer) {
            $('.visible-fertilizer-function').show();
        } else {
            $('.visible-fertilizer-function').hide();
        }
        return true;
    };

    //load fertilizer map, load by layer, load fertility map.
    module.loadMap = function (layerId, type, userId, guess_map) {
        gisObject.is_show_all_fertility = false;
        if (type != undefined) {
            gisObject.is_fertilizer = (type == 'layer_fertilizer'||type == 'layer_fertilizer_hidden');
        }

        var postData = {};
        if (userId) {
            postData.userId = userId;
            //set value to indicate user is in show all fertility mode
            gisObject.is_show_all_fertility = true;
        } else {
            if (gisObject.is_fertilizer) {
                postData.is_fertilizer = true;
            }
            postData.layerId = layerId;
        }
        if (gisObject.mode_type == 2) {
            postData.mode_selection_ids = gisObject.mode_selection_ids;
            gisObject.mode_selection_ids = [];
        }
        module.mapType = type;
        module.userId = userId;


        $.post(
            window.base_url + '/api/show-map', postData,
            function (data) {
                if (data == 'null') {
                    module.defaultMap();
                    return;
                }
                //using in case display fertilizer map in a popup for guess.
                if (guess_map != undefined) {
                    module._processMapRender($.parseJSON(data), {
                        mapElement: $('#mapPrediction'),
                        controlToolBar: $('#controlToolBarPopup'),
                        legendElement: $('#legendPopup'),
                        typeDrawElement: $('#typeDrawPopup'),
                        clearButton: $('#clearPopup')
                    });
                    module.typeDrawElement.find('button[value="LineString"]').trigger('click');
                    gisObject.guess_prediction.vector = true;

                } else {
                    var tmpdata = $.parseJSON(data);
                    module.central = tmpdata.central;
                    if(!tmpdata.scale_type){
                        module.layerID = layerId;
                    }
                    module._processMapRender(tmpdata);
                }
            });
        $('#clear').hide();

        //fix for
    };

    module.debugMap = function (layer1, layer2, layer3) {
        module._clear();
        // module._showLayerFromJson(layer1, { color : { Polygon : 'green' } });
        module._showLayerFromJson(layer2, {color: {GeometryCollection: 'red'}});
        module._showLayerFromJson(layer3, {color: {GeometryCollection: 'yellow'}});
    };

    module.storeGuestMap = function (geoJson) {
        module.map.removeInteraction(module.drag);
        gisObject.guess_prediction.polygons = $.parseJSON(geoJson);
    };

    //function call from outside class to render a popup which will display outside map.
    // now using in 2 case, export pdf and guess map

    module.loadExportMap = function (layer_id, guess_map) {

        var page_url = (guess_map) ? window.base_url + "/fertilization-out-prediction/" + layer_id : window.base_url + '/map-prefix/export/' + layer_id;

        //display popup.
        $.fancybox([{
            href: page_url,
            type: 'ajax',
            keys : {
                close  : null
            },
            'beforeClose': function () {
                window.location.reload();
            },
            helpers: {
                overlay: {
                    closeClick: false
                }
            }
        }], {
            afterLoad: function (data) {
                reloadPage(data);
            }
        }, {
            afterClose: function () {
                //we change the current gisMap Object elements to display a map in popup.
                //so we need to reload if close.
                window.location.reload();
            }
        });
    };
    module.loadOutside = function (config) {

        module.mapElement = $('#map_export');

        var legendElement = $('#legend_export');
        module.mapElement.html('').show();

        //set map projection.
        var projection = new ol.proj.Projection({
            code: 'EPSG:' + module.defaultEPSG,
            extent: module.defaultExtend,
            //units: 'm',
            getPointResolution: function (resolution, point) {
                return resolution;
            }
        });
        ol.proj.addProjection(projection);

        if (config.legend) {
            legendElement.show()
                .find('img:first')
                .attr('src',
                window.mapserver + config.map_infos.file_name + '?version=1.1.1&service=WMS&request=GetLegendGraphic&layer=' + config.map_infos.layers + '&format=image/png');
        } else {
            legendElement.hide();
        }
        var layers = [
            new ol.layer.Tile({
                source: new ol.source.TileWMS({
                    url: window.mapserver + config.map_infos.file_name,
                    crossOrigin: '',
                    params: {
                        'LAYERS': module.raster + ',' + config.map_infos.layers,
                        'VERSION': '1.1.1'
                    }
                }),
                showLegend: true
            })

        ];

        module.map = new ol.Map({
            interactions: ol.interaction.defaults({mouseWheelZoom: false, doubleClickZoom: false}),
            controls: [
                //new ol.control.ScaleLine({}),
                new ol.control.Zoom({delta: 1}),
                new ol.control.MousePosition({
                    undefinedHTML: '',
                    projection: 'EPSG:' + module.worldEPSG,
                    coordinateFormat: function (coordinate) {
                        var langLotCoordinate = ol.coordinate.format(coordinate, '{x}, {y}', 4);
                        var langLot = langLotCoordinate.split(',');
                        var lat = module._latToDMS(langLot[1]);
                        var long = module._lngToDMS(langLot[0]);
                        return lat + ' , ' + long;
                    }
                })
            ],
            minScale: 250000,
            maxResolution: "auto",
            layers: layers,
            target: 'map_export',
            view: new ol.View({
                projection: projection,
                center: config.map_infos.central,
                zoom: module.minZoom
                //maxZoom: module.maxZoom,
                //minZoom: module.minZoom,
            })
        });
        module.mapElement.find('canvas').mousewheel(function(event, delta) {
            $('#map').find('.ol-overlaycontainer-stopevent').remove();
            $('#map').find('.ol-overlaycontainer').remove();
            module._wheel();

        });


    };

    module.guessMap = function (layer_id) {
        module.loadMap(layer_id, 'layer_fertilizer', null, true);
    };

    //function to generate lines for fertilizer map, call when click ok button.
    //call this function to show the shortage location of fertilizer for end-user
    module.generate = function () {
        if (module.isValidate()) {
            if (
                (gisObject.guess_prediction.fertilizer_map_id != undefined) &&
                (gisObject.guess_prediction.fertilizer_map_id != '') &&
                (gisObject.guess_prediction.polygons != undefined) &&
                (gisObject.guess_prediction.polygons != '') &&
                (gisObject.guess_prediction.line != undefined) &&
                (gisObject.guess_prediction.line != '') &&
                (gisObject.guess_prediction.point != undefined) &&
                (gisObject.guess_prediction.point != '')
            ) {
                var barrelType = 1;
                if ($('#sub-tank').length) {
                    barrelType = 2;
                }
                openDialog('/fertilization-predict-popup/' + barrelType, 500, 500);
                $.post(window.base_url + '/generate-guess-direction', gisObject.guess_prediction).then(function (data) {

                    module._showLayerFromJson(data.data, {
                        color: {LineString: 'black'},
                        arrow: true,
                        point_clear: true
                    });
                    if (barrelType == 2) {
                        module.appendHtml(data.predictionData);
                    }
                    else {
                        module.appendHtmlOneBarrel(data.predictionData);
                    }


                });
            } else {
                return fancyAlert(Lang.get('common.fertilization_prediction_invalid_data'), window.info_title);
            }
        }

    };

    //function append html element for fertilization out prediction popup
    module.appendHtml = function (objJSON) {
        //each item of prediction data likes
        //{"main":15.219,"sub":0,"geo":{"type":"LineString","coordinates":[[15921453.169,5332110
        //.6458927],[15921274.713741,5331749.3561038]]},"detail_info":[{"main":16.02,"sub":0,"id":"86484"},{"main"
        //:136.17,"sub":0,"id":"86485"}]}
        var parentElement = $("#rowdata");
        var number = 1, count = 0, tempTotalMain = 0, tempTotalSub = 0, j = 0;
        var tempMain, tempSub, tempGeo, tempDetailInfo;
        var totalMain = 0, totalSub = 0;
        var text1 = '肥料補充';
        var text2 = 'メイン肥料不足';
        var text3 = 'サブ肥料不足';
        var text4 = 'メイン肥料不足 - サブ肥料不足';
        var maxMain = $('#main-tank').val();
        var maxSub = $('#sub-tank').val();
        var shortageLocationStyleForText = "color: red;vertical-align: middle;";
        var shortageLocationStyleForBackGround = "background-color: red;vertical-align: middle;";
        var lastData = [];
        //objJSON = objJSON.reverse();
        $.each(objJSON, function (i, item) {
            count++;
        });

        $.each(objJSON, function (i, item) {
            lastData[j] = [];
            //store linestring object
            lastData[j].geo = item['main'];
            //store detail info
            lastData[j].detail_info = item['detail_info'];
            if (i % 2 == 0) {
                tempMain = parseInt(item['main'].toFixed(0));
                tempSub = parseInt(item['sub'].toFixed(0));
                tempGeo = item["geo"];
                tempDetailInfo = item["detail_info"];
                if (count % 2 != 0 && i == (count - 1)) {
                    lastData[j].main1 = tempMain;
                    lastData[j].main2 = null;
                    lastData[j].sub1 = tempSub;
                    lastData[j].sub2 = null;
                    lastData[j].geo1 = tempGeo;
                    lastData[j].geo2 = null;
                    lastData[j].detailInfo1 = tempDetailInfo;
                    lastData[j].detailInfo2 = null;
                    j++;
                }
            } else {
                lastData[j].main1 = parseInt(tempMain);
                lastData[j].main2 = parseInt(item['main'].toFixed(0));
                lastData[j].sub1 = parseInt(tempSub);
                lastData[j].sub2 = parseInt(item['sub'].toFixed(0));
                lastData[j].geo1 = tempGeo;
                lastData[j].geo2 = item["geo"];
                lastData[j].detailInfo1 = tempDetailInfo;
                lastData[j].detailInfo2 = item["detail_info"];
                j++;

            }
        });

        $("#maxMain").text(maxMain + 'kg');
        $("#maxSub").text(maxSub + 'kg');
        //declare an object to store mesh id and linestring
        //to mark shortage location of fertilizer
        var jsonMarkPredictionData = {};
        jsonMarkPredictionData.items = [];
        $.each(lastData, function (i, item) {
            //add tr3 to parent element
            if (i == 0) {
                tempTotalMain += (item['main1'] + item['main2']);
                tempTotalSub += (item['sub1'] + item['sub2']);
            } else if (i > 0 && i <= count) {
                var tr3 = document.createElement("tr");
                var td10 = document.createElement("td");
                var td11 = document.createElement("td");
                var td12 = document.createElement("td");
                var td13 = document.createElement("td");
                tempTotalMain += (item['main1'] + item['main2']);
                tempTotalSub += (item['sub1'] + item['sub2']);
                //add data and css for each td of tr3
                $(td10).html('&nbsp;');
                if (tempTotalMain > maxMain) {
                    $(td11).text(text1);
                    $(td11).attr("style", "background-color: yellow;");
                    tempTotalMain = (item['main1'] + item['main2']);
                }
                if (tempTotalSub > maxSub) {
                    $(td12).text(text1);
                    $(td12).attr("style", "background-color: yellow;");
                    tempTotalSub = (item['sub1'] + item['sub2']);
                }
                $(td11).attr("colspan", "2");
                $(td12).attr("colspan", "2");
                $(td13).attr("colspan", "2");

                //append td to tr
                $(tr3).append(td10, td11, td12, td13);
                //append tr to parent element
                parentElement.append(tr3);
            }

            //add tr1,tr2 to parent element
            var td3Value = 0;
            var td5Value = 0;

            //build a tr and append it to parent element
            var tr1 = document.createElement("tr");
            var tr2 = document.createElement("tr");
            var td1 = document.createElement("td");
            var td2 = document.createElement("td");
            var td3 = document.createElement("td");
            var td4 = document.createElement("td");
            var td5 = document.createElement("td");
            var td6 = document.createElement("td");
            var td7 = document.createElement("td");
            var td8 = document.createElement("td");
            var td9 = document.createElement("td");

            //add data for each td of tr1, tr2
            $(td1).text(number);
            number++;

            if (item["main2"] != null) {
                td3Value = (item["main1"] + item["main2"]);
                td5Value = (item["sub1"] + item["sub2"]);
                $(td7).text(number);
                number++;
                $(td8).text(item["main2"]);
                $(td9).text(item["sub2"]);
            } else {
                td3Value = item["main1"];
                td5Value = item["sub1"];
                $(td7).html('&nbsp;');
                $(td8).html('&nbsp;');
                $(td9).html('&nbsp;');
            }

            $(td2).text(item["main1"]);
            $(td3).attr("rowspan", "2");
            $(td3).attr("style", "vertical-align: middle;");
            $(td3).text(td3Value);
            $(td4).text(item["sub1"]);
            $(td5).attr("rowspan", "2");
            $(td5).attr("style", "vertical-align: middle;");
            $(td5).text(td5Value);
            $(td6).attr("rowspan", "2");
            $(td6).attr("style", "vertical-align: middle;");
            if (td3Value > maxMain && td5Value > maxSub) {
                //lack of fertilizer in both main & sub barrel
                module.calculateShortageLocation(item, jsonMarkPredictionData, maxMain, maxSub);
                $(td6).text(text4);
                $(td6).attr("style", shortageLocationStyleForText);
                $(td3).attr("style", shortageLocationStyleForBackGround);
                $(td5).attr("style", shortageLocationStyleForBackGround);
            } else if (td3Value > maxMain || td5Value > maxSub) {
                $(td6).attr("style", shortageLocationStyleForText);
                if (td3Value > maxMain) {
                    //lack of fertilizer in main barrel
                    module.calculateShortageLocation(item, jsonMarkPredictionData, maxMain, 0);
                    $(td6).text(text2);
                    $(td3).attr("style", shortageLocationStyleForBackGround);
                }
                else {
                    //lack of fertilizer in sub barrel
                    module.calculateShortageLocation(item, jsonMarkPredictionData, 0, maxSub);
                    $(td6).text(text3);
                    $(td5).attr("style", shortageLocationStyleForBackGround);
                }
            }

            //append to element parent
            $(tr1).append(td1, td2, td3, td4, td5, td6);
            $(tr2).append(td7, td8, td9);
            parentElement.append(tr1);
            parentElement.append(tr2);

            totalMain += td3Value;
            totalSub += td5Value;
        });
        var tr = document.createElement("tr");
        var td1 = document.createElement("td");
        var td2 = document.createElement("td");
        var td3 = document.createElement("td");
        var td4 = document.createElement("td");
        $(td1).text('合計');
        $(td2).attr("colspan", "2");
        $(td3).attr("colspan", "2");
        $(td4).attr("colspan", "2");
        $(td2).text(totalMain);
        $(td3).text(totalSub);
        $(tr).append(td1, td2, td3, td4);
        parentElement.append(tr);
        //gis
        //call display with mesh id, linestring

        //TODO : remove 3 lines :  module._clear();  module._showLayerFromJson(data.squares);module._showLayerFromJson(data.lines);
        $.post(window.base_url + '/display-final', jsonMarkPredictionData, function (data) {
            //module._clear();
            module._showLayerFromJson(data.points, {final: true, point_clear: true});
            //module._showLayerFromJson(data.squares);
            //module._showLayerFromJson(data.lines);
        });
    };

    module.appendHtmlOneBarrel = function (objJSON) {
        //each item of prediction data likes
        //{"main":15.219,"sub":0,"geo":{"type":"LineString","coordinates":[[15921453.169,5332110
        //.6458927],[15921274.713741,5331749.3561038]]},"detail_info":[{"main":16.02,"sub":0,"id":"86484"},{"main"
        //:136.17,"sub":0,"id":"86485"}]}
        var parentElement = $("#rowdata");
        var number = 1, count = 0, tempTotalMain = 0, j = 0;
        var tempMain, tempGeo, tempDetailInfo;
        var totalMain = 0;
        var text1 = '肥料補充';
        var text2 = 'メイン肥料不足';
        var maxMain = $('#main-tank').val();
        var shortageLocationStyleForText = "color: red;vertical-align: middle;";
        var shortageLocationStyleForBackGround = "background-color: red;vertical-align: middle;";
        var lastData = [];
        //objJSON = objJSON.reverse();
        $.each(objJSON, function (i, item) {
            count++;
        });

        $.each(objJSON, function (i, item) {
            lastData[j] = [];
            //store linestring object
            lastData[j].geo = item['main'];
            //store detail info
            lastData[j].detail_info = item['detail_info'];
            if (i % 2 == 0) {
                tempMain = parseInt(item['main'].toFixed(0));
                tempGeo = item["geo"];
                tempDetailInfo = item["detail_info"];
                if (count % 2 != 0 && i == (count - 1)) {
                    lastData[j].main1 = tempMain;
                    lastData[j].main2 = null;
                    lastData[j].geo1 = tempGeo;
                    lastData[j].geo2 = null;
                    lastData[j].detailInfo1 = tempDetailInfo;
                    lastData[j].detailInfo2 = null;
                    j++;
                }
            } else {
                lastData[j].main1 = parseInt(tempMain);
                lastData[j].main2 = parseInt(item['main'].toFixed(0));
                lastData[j].geo1 = tempGeo;
                lastData[j].geo2 = item["geo"];
                lastData[j].detailInfo1 = tempDetailInfo;
                lastData[j].detailInfo2 = item["detail_info"];
                j++;

            }
        });

        $("#maxMain").text(maxMain + 'kg');
        //declare an object to store mesh id and linestring
        //to mark shortage location of fertilizer
        var jsonMarkPredictionData = {};
        jsonMarkPredictionData.items = [];
        $.each(lastData, function (i, item) {
            //add tr4 to parent element
            //it contains real data when calculating
            if (i == 0) {
                tempTotalMain += (item['main1'] + item['main2']);
            } else if (i > 0 && i <= count) {
                var tr4 = document.createElement("tr");
                var td10 = document.createElement("td");
                var td11 = document.createElement("td");
                var td13 = document.createElement("td");
                tempTotalMain += (item['main1'] + item['main2']);
                //add data and css for each td of tr4
                $(td10).html('&nbsp;');
                if (tempTotalMain > maxMain) {
                    $(td11).text(text1);
                    $(td11).attr("style", "background-color: yellow;");
                    tempTotalMain = (item['main1'] + item['main2']);
                }
                $(td11).attr("colspan", "2");
                $(td13).attr("colspan", "2");

                //append td to tr
                $(tr4).append(td10, td11, td13);
                //append tr to parent element
                parentElement.append(tr4);
            }

            //add tr1,tr2 to parent element
            var td3Value = 0;

            //build a tr and append it to parent element
            var tr1 = document.createElement("tr");
            var tr2 = document.createElement("tr");

            var td1 = document.createElement("td");
            var td2 = document.createElement("td");
            var td3 = document.createElement("td");
            var td4 = document.createElement("td");

            var td7 = document.createElement("td");
            var td8 = document.createElement("td");

            //add data for each td of tr1, tr2
            $(td1).text(number);
            number++;

            if (item["main2"] != null) {
                td3Value = (item["main1"] + item["main2"]);
                $(td7).text(number);
                number++;
                $(td8).text(item["main2"]);
            } else {
                td3Value = item["main1"];
                $(td7).html('&nbsp;');
                $(td8).html('&nbsp;');
            }

            $(td2).text(item["main1"]);
            $(td3).attr("rowspan", "2");
            $(td3).attr("style", "vertical-align: middle;");
            $(td3).text(td3Value);
            $(td4).attr("rowspan", "2");
            if (td3Value > maxMain) {
                //lack of fertilizer in both main & sub barrel
                module.calculateShortageLocationOneBarrel(item, jsonMarkPredictionData, maxMain);
                $(td4).text(text2);
                $(td4).attr("style", shortageLocationStyleForText);
                $(td3).attr("style", shortageLocationStyleForBackGround);
            }

            //append to element parent
            $(tr1).append(td1, td2, td3, td4);
            $(tr2).append(td7, td8);
            parentElement.append(tr1);
            parentElement.append(tr2);
            totalMain += td3Value;
        });
        var tr = document.createElement("tr");
        var td1 = document.createElement("td");
        var td2 = document.createElement("td");
        var td4 = document.createElement("td");
        $(td1).text('合計');
        $(td2).attr("colspan", "2");
        $(td4).attr("colspan", "2");
        $(td2).text(totalMain);
        $(tr).append(td1, td2, td4);
        parentElement.append(tr);
        //gis
        //call display with mesh id, linestring

        //TODO : remove 3 lines :  module._clear();  module._showLayerFromJson(data.squares);module._showLayerFromJson(data.lines);
        $.post(window.base_url + '/display-final', jsonMarkPredictionData, function (data) {
            //module._clear();
            module._showLayerFromJson(data.points, {final: true, point_clear: true});
            //module._showLayerFromJson(data.squares);
            //module._showLayerFromJson(data.lines);
        });
    };

    //function validate form fertilization out prediction
    module.isValidate = function () {
        return $('.form-fertilization-predict').validationEngine('validate', {
            showOneMessage: true,
            onValidationComplete: function (form, status) {
                setTimeout(function () {
                    $('.form-fertilization-predict').validationEngine('hideAll');
                }, 4000);
                return status;
            }
        });
    };
    //function to find out the shortage location
    //it returns the mesh id and linestring object
    module.calculateShortageLocation = function (data, jsonMarkPredictionData, maxMain, maxSub) {
        //format data {"main1","main2","sub1","sub2","geo1","geo2","detailInfo1","detailInfo2"}
        var tmpMain = 0, tmpSub = 0;
        var checkPredictMain = false;
        var checkPredictSub = false;
        var mesh = {
            "geo": '',
            "id": ''
        };

        if (maxMain > 0 && maxSub > 0) {
            //lack of fertilizer in both main and sub
            //check path 1
            $.each(data["detailInfo1"], function (i, item) {
                tmpMain += item["main"];
                tmpSub += item["sub"];
                //lack of fertilizer in main
                if (tmpMain > maxMain && checkPredictMain == false) {
                    checkPredictMain = true;
                    mesh = {
                        "geo": data["geo1"],
                        "id": item["id"]
                    };
                    jsonMarkPredictionData.items.push(mesh);
                }
                //lack of fertilizer in sub
                if (tmpSub > maxSub && checkPredictSub == false) {
                    checkPredictSub = true;
                    mesh = {
                        "geo": data["geo1"],
                        "id": item["id"]
                    };
                    jsonMarkPredictionData.items.push(mesh);
                }

                if (checkPredictMain == true && checkPredictSub == true)
                    return false;
            });
            //check path 2
            if (checkPredictMain == false) {
                $.each(data["detailInfo2"], function (i, item) {
                    tmpMain += item["main"];
                    //lack of fertilizer in main
                    if (tmpMain > maxMain) {
                        checkPredictMain = true;
                        mesh = {
                            "geo": data["geo2"],
                            "id": item["id"]
                        };
                        jsonMarkPredictionData.items.push(mesh);
                        return false;
                    }
                });
            }

            if (checkPredictSub == false) {
                $.each(data["detailInfo2"], function (i, item) {
                    tmpSub += item["sub"];
                    //lack of fertilizer in sub
                    if (tmpSub > maxSub && checkPredictSub == false) {
                        checkPredictSub = true;
                        mesh = {
                            "geo": data["geo2"],
                            "id": item["id"]
                        };
                        jsonMarkPredictionData.items.push(mesh);
                        return false;
                    }
                });
            }
        }
        else if (maxMain == 0 && maxSub > 0) {
            //lack of fertilizer in sub only
            $.each(data["detailInfo1"], function (i, item) {
                tmpSub += item["sub"];
                //lack of fertilizer in main
                if (tmpSub > maxSub) {
                    checkPredictSub = true;
                    mesh = {
                        "geo": data["geo1"],
                        "id": item["id"]
                    };
                    jsonMarkPredictionData.items.push(mesh);
                    return false;
                }
            });

            if (checkPredictSub == false) {
                $.each(data["detailInfo2"], function (i, item) {
                    tmpSub += item["sub"];
                    //lack of fertilizer in main
                    if (tmpSub > maxSub) {
                        checkPredictSub = true;
                        mesh = {
                            "geo": data["geo2"],
                            "id": item["id"]
                        };
                        jsonMarkPredictionData.items.push(mesh);
                        return false;
                    }
                });
            }
        }
        else if (maxSub == 0 && maxMain > 0) {
            //lack of fertilizer in main only
            $.each(data["detailInfo1"], function (i, item) {
                tmpMain += item["main"];
                //lack of fertilizer in main
                if (tmpMain > maxMain) {
                    checkPredictMain = true;
                    mesh = {
                        "geo": data["geo1"],
                        "id": item["id"]
                    };
                    jsonMarkPredictionData.items.push(mesh);
                    return false;
                }
            });

            if (checkPredictMain == false) {
                $.each(data["detailInfo2"], function (i, item) {
                    tmpMain += item["main"];
                    //lack of fertilizer in main
                    if (tmpMain > maxMain) {
                        checkPredictMain = true;
                        mesh = {
                            "geo": data["geo2"],
                            "id": item["id"]
                        };
                        jsonMarkPredictionData.items.push(mesh);
                        return false;
                    }
                });
            }
        }
        return jsonMarkPredictionData;
    };

    module.calculateShortageLocationOneBarrel = function (data, jsonMarkPredictionData, maxMain) {
        //format data {"main1","main2","sub1","sub2","geo1","geo2","detailInfo1","detailInfo2"}
        var tmpMain = 0, tmpSub = 0;
        var checkPredictMain = false;
        var mesh = {
            "geo": '',
            "id": ''
        };

        if (maxMain > 0) {
            //lack of fertilizer in both main and sub
            //check path 1
            $.each(data["detailInfo1"], function (i, item) {
                tmpMain += item["main"];
                //lack of fertilizer in main
                if (tmpMain > maxMain && checkPredictMain == false) {
                    checkPredictMain = true;
                    mesh = {
                        "geo": data["geo1"],
                        "id": item["id"]
                    };
                    jsonMarkPredictionData.items.push(mesh);
                }
                if (checkPredictMain == true)
                    return false;
            });
            //check path 2
            if (checkPredictMain == false) {
                $.each(data["detailInfo2"], function (i, item) {
                    tmpMain += item["main"];
                    //lack of fertilizer in main
                    if (tmpMain > maxMain) {
                        checkPredictMain = true;
                        mesh = {
                            "geo": data["geo2"],
                            "id": item["id"]
                        };
                        jsonMarkPredictionData.items.push(mesh);
                        return false;
                    }
                });
            }
        }
        return jsonMarkPredictionData;
    };

})(gisMap = {});
