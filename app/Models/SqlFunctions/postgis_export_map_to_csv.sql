/*
  Export to CSV
 */

DROP FUNCTION IF EXISTS ST_ExportToCsv(geometry, INT4);
CREATE OR REPLACE FUNCTION ST_ExportToCsv(geo geometry, srid INT4)
  RETURNS SETOF text AS
  $BODY$
  DECLARE
    temp_text    text;
    temp_geo GEOMETRY;

    new_Xmin     INT;
    new_Ymin     INT;
    new_Xmax     INT;
    new_Ymax     INT;
  BEGIN

    temp_geo := ST_Transform(geo, srid);

    new_Xmin := (ROUND(ST_Xmin(temp_geo)))::INT;
    new_Ymin := (ROUND(ST_Ymin(temp_geo)))::INT;

    new_Xmax := (ROUND(ST_Xmax(temp_geo)))::INT;
    new_Ymax := (ROUND(ST_Ymax(temp_geo)))::INT;

    temp_text := new_Ymin || ',' || new_Xmin || ',' || new_Ymax || ',' || new_Xmax;

    RETURN NEXT temp_text;
  END;
  $BODY$
LANGUAGE plpgsql IMMUTABLE;