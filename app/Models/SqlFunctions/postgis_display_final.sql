/*
  get display points for prediction.
 */

DROP FUNCTION IF EXISTS ST_displayFinal( geometry, geometry );
CREATE OR REPLACE FUNCTION ST_displayFinal(geo geometry, line_string geometry)
  RETURNS json AS
  $BODY$
  DECLARE
  temp_central geometry;
  BEGIN

   IF (st_intersects(geo, line_string)) THEN
     RETURN st_asgeojson(st_centroid(ST_intersection(geo, line_string)));
   ELSE
     temp_central := st_centroid(geo);

     RETURN st_asgeojson(st_closestpoint(line_string, temp_central));
   END IF;

  END;
  $BODY$
LANGUAGE plpgsql IMMUTABLE;