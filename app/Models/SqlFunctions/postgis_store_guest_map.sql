/*
  Stores polygon (which create from multi parallel linestring) and intersection between fertilizer map meshes and each polygons.
 */

-- create new type (mix : intersections, geo : geometry of polygon)
DROP TYPE IF EXISTS A_Grid CASCADE;
CREATE TYPE A_Grid AS (
  geo json,
  store json
);
DROP FUNCTION IF EXISTS ST_StoreGuestMap( geometry, geometry );
CREATE OR REPLACE FUNCTION ST_StoreGuestMap(build_area geometry, geo_collect geometry)
  RETURNS SETOF A_Grid AS
  $BODY$
  DECLARE
    v_grid    A_Grid;
    i         GEOMETRY;
  BEGIN
    -- Dump geo_collect which is json layer get from St_MakeParallel to polygons.
    FOR i IN
    SELECT (ST_Dump(geo_collect)).geom
    LOOP
      IF (ST_intersects(st_setsrid(i, 3857), build_area))
      THEN
        -- get intersection between each polygon and current fertilizer map.
        v_grid.geo := ST_AsGeoJSON(st_intersection(st_setsrid(i, 3857), build_area));
        v_grid.store := ST_AsGeoJSON(st_setsrid(i, 3857));
        RETURN NEXT v_grid;
      END IF;
    END LOOP;
  END;
  $BODY$
LANGUAGE plpgsql IMMUTABLE;