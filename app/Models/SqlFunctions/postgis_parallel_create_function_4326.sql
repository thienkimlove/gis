
/*
   This function using to create multi parallels linestring base on (line_string).
   Those linestring with have a step (distance).
   And must intersect with current fertilizer map (build_area)
 */
DROP FUNCTION IF EXISTS ST_MakeParallel( geometry, geometry, NUMERIC, NUMERIC, NUMERIC, INT4 );
CREATE OR REPLACE FUNCTION ST_MakeParallel(build_area geometry, line_string geometry, distance NUMERIC, field_width NUMERIC, fid NUMERIC, srid INT4)
  RETURNS SETOF GEOMETRY AS
  $BODY$
  DECLARE
    temp_distance NUMERIC;
    temp_geo      GEOMETRY;
    temp_point    NUMERIC;
    temp_grid     geometry;
    temp_build    GEOMETRY;
    v_loCol       INT4;
    v_hiCol       INT4;
    startLine GEOMETRY;
    endLine GEOMETRY;
    temp_central GEOMETRY;
    startPoint GEOMETRY;
    endPoint GEOMETRY;
    line1 GEOMETRY;
    line2 GEOMETRY;
    start_line GEOMETRY;
    end_line GEOMETRY;
    loop_line GEOMETRY;
    loop_line1 GEOMETRY;
    loop_line2 GEOMETRY;


    azi1 NUMERIC;
    azi2 NUMERIC;
    lineLength NUMERIC;
    temp2_distance NUMERIC;
  BEGIN
    --set srid json geo to 3957
    line_string := st_setsrid(line_string, 3857);
    build_area := st_setsrid(build_area, 3857);


    startLine := st_startpoint(line_string);
    endLine := st_endpoint(line_string);
    azi1 := ST_Azimuth(startLine, endLine);
    azi2 := ST_Azimuth(endLine, startLine);
    temp_central := st_centroid(build_area);

    --get line length
    lineLength := (st_length(line_string)/2) +5;
    --create 2 linestrings which parallel with current string
    startPoint := st_setsrid( ST_TRANSLATE(temp_central, sin(azi1) * lineLength, cos(azi1) * lineLength), 3857);
    endPoint := st_setsrid(ST_TRANSLATE(temp_central, sin(azi2) * lineLength, cos(azi2) * lineLength), 3857);
    temp_geo := st_setsrid(st_makeline(startPoint, endPoint), 3857);
    --create 2 lines which parallel with current line.
    temp_distance := CEIL((field_width/2)::NUMERIC);

    line1 := st_transform(ST_OffsetCurve(st_transform(temp_geo, srid), temp_distance), 3857);
    line2 := st_transform(ST_OffsetCurve(st_transform(temp_geo, srid), (0 - temp_distance)), 3857);

    IF (st_distance(line1, line_string) > st_distance(line2, line_string)) THEN
      start_line := line2;
      end_line := line1;
    ELSE
      start_line := line1;
      end_line := line2;
    END IF;
    loop_line := start_line;

    v_loCol := 0;
    v_hiCol := CEIL((field_width / distance) :: NUMERIC) -1;
    FOR v_col IN v_loCol..v_hiCol LOOP
         temp_build:= loop_line;
         temp2_distance := v_col*distance + distance;
         loop_line1 := st_transform(ST_OffsetCurve(st_transform(start_line, srid), temp2_distance), 3857);
         loop_line2 := st_transform(ST_OffsetCurve(st_transform(start_line, srid), (0 - temp2_distance)), 3857);
         IF (st_distance(loop_line1, end_line) > st_distance(loop_line2, end_line)) THEN
            loop_line := loop_line2;
         ELSE
           loop_line := loop_line1;
         END IF;
         temp_grid := ST_ConvexHull(ST_Collect(loop_line, temp_build));
         RETURN NEXT temp_grid;
    END LOOP;
  END;
  $BODY$
LANGUAGE plpgsql IMMUTABLE;