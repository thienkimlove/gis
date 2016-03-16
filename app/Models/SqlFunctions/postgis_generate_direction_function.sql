CREATE OR REPLACE FUNCTION array_reverse(anyarray) RETURNS anyarray AS $$
SELECT ARRAY(
    SELECT $1[i]
    FROM generate_subscripts($1,1) AS s(i)
    ORDER BY i DESC
);
$$ LANGUAGE 'sql' STRICT IMMUTABLE;

/*
  function to guess by building lines and mix intersections between each mesh of fertilizer map with those line.

 */
DROP TYPE IF EXISTS D_Grid CASCADE;
CREATE TYPE D_Grid AS (
  endpoint json,
  geo json,
  len NUMERIC,
  mix json
);
DROP FUNCTION IF EXISTS ST_generateDirection( geometry, geometry, geometry, geometry, NUMERIC, INT4);
-- Now create the function
CREATE OR REPLACE FUNCTION ST_generateDirection(a_point geometry, a_line geometry, a_polygons geometry, build_area geometry, fid NUMERIC, srid INT4)
  RETURNS SETOF D_Grid AS
  $BODY$
  DECLARE
     v_grid D_Grid;
     i geometry;
     temp_central geometry;
     temp_distance NUMERIC;
     temp_str GEOMETRY;
     draw geometry;
     temp_endpoint GEOMETRY ;
     dis1 NUMERIC;
     dis2 NUMERIC;
     temp_geo geometry;
     startPoint geometry;
     endPoint geometry;
     startLine GEOMETRY;
     endLine GEOMETRY;
     azi1 NUMERIC;
     azi2 NUMERIC;
     lineLength NUMERIC;
     temp_dump geometry[];
     tables CURSOR FOR SELECT
                          id,
                         main_fertilizer,
                         sub_fertilizer,
                          st_transform(geo, srid) AS geo
                        FROM fertilizer_map_infos
                        WHERE fertilizer_id = fid;
     row       fertilizer_map_infos%ROWTYPE;
    temp_char NUMERIC;
    temp_mix  TEXT [];
    b_geo GEOMETRY;
    temp_i GEOMETRY;
    temp_check NUMERIC;
  BEGIN
    -- to avoid unknown SRID error when get geometry from json
    build_area := st_setsrid(build_area, 3857);
    a_point := st_setsrid(a_point, 3857);
    a_line := st_setsrid(a_line, 3857);
    a_polygons := st_setsrid(a_polygons, 3857);
    -- first point to start draw.
    draw := a_point;

    -- calculate azimuth of line in both direction
    startLine := st_startpoint(a_line);
    endLine := st_endpoint(a_line);
    azi1 := ST_Azimuth(startLine, endLine);
    azi2 := ST_Azimuth(endLine, startLine);

    --get line length
    lineLength := (st_length(a_line)/2) +5;
    -- start dump list of polygons grid to each polygon
    FOR i IN
    SELECT (ST_Dump(a_polygons)).geom
    LOOP
      -- set SRID of each polygon
      temp_dump := array_append(temp_dump, st_setsrid(i, 3857));
    END LOOP;

    -- check if we must reserve this array or not.
    -- RAISE NOTICE 'reserver %', st_distance(a_point, temp_dump[array_lower(temp_dump, 1)]);
    -- RAISE NOTICE 'reserver1 %', st_distance(a_point, temp_dump[array_upper(temp_dump, 1)]);

    IF (st_distance(a_point, temp_dump[array_lower(temp_dump, 1)]) > st_distance(a_point, temp_dump[array_upper(temp_dump, 1)])) THEN
      temp_dump := array_reverse(temp_dump);
    END IF;

    FOREACH temp_i IN ARRAY temp_dump
    LOOP
      -- only work with polygon have intersect with our fertilizer map.
      IF (ST_Intersects(temp_i, build_area)) THEN
          -- get central of polygon
          temp_central := ST_Centroid(temp_i);
          -- get the start and end point of line from length/2 and central point.
          startPoint := st_setsrid( ST_TRANSLATE(temp_central, sin(azi1) * lineLength, cos(azi1) * lineLength), 3857);
          endPoint := st_setsrid(ST_TRANSLATE(temp_central, sin(azi2) * lineLength, cos(azi2) * lineLength), 3857);
          temp_geo := st_setsrid(st_makeline(startPoint, endPoint), 3857);
          -- find which is end of arrow
          dis1 :=  st_distance(draw, startPoint);
          dis2 :=  st_distance(draw, endPoint);
          IF (dis1 > dis2) THEN
            temp_endpoint := startPoint;
          ELSE
            temp_endpoint := endPoint;
          END IF;
          -- reset start draw point for next line.
          draw := temp_endpoint;
          -- get endpoint of arrow in array json.
          v_grid.endpoint := array_to_json(ARRAY[ST_X(temp_endpoint), ST_Y(temp_endpoint)]);
          -- get line geometry in json.
          v_grid.geo := ST_AsGeoJSON(temp_geo);
          -- line length in real metre.
          v_grid.len := st_length(St_Intersection(st_transform(temp_geo, srid), st_transform(build_area, srid)));
           -- working with mix
          b_geo := st_transform(temp_i, srid);
          temp_mix := '{}';
          -- check the intersection between each mesh of fertilizer map and current polygon.
          FOR row IN tables LOOP
            IF (ST_Intersects(row.geo, b_geo))
            THEN
              temp_char := ((ST_Area(St_Intersection(b_geo, row.geo)))/ST_Area(row.geo)):: NUMERIC;
              temp_char := round(temp_char :: NUMERIC, 2);
              IF (temp_char > 0.00)
              THEN
                temp_check := st_distance(st_transform(temp_endpoint, srid), row.geo);
                temp_mix := array_append(temp_mix, array_to_string(ARRAY [temp_char, (row.id) :: NUMERIC,
                (row.main_fertilizer)::NUMERIC,
                (row.sub_fertilizer)::NUMERIC, st_distance(st_transform(temp_endpoint, srid), row.geo)], ','));
              END IF;
            END IF;
          END LOOP;
          v_grid.mix := array_to_json(temp_mix);
          RETURN NEXT v_grid;
      END IF;
    END LOOP;
  END;
  $BODY$
LANGUAGE plpgsql IMMUTABLE;