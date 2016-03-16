DROP TYPE IF EXISTS R_Grid CASCADE;
CREATE TYPE R_Grid AS (
  mix JSON,
  geo GEOMETRY,
  code JSON
);
-- Function: st_makegrid(geometry, numeric, numeric, integer)

-- DROP FUNCTION st_makegrid(geometry, numeric, numeric, integer);

CREATE OR REPLACE FUNCTION st_makegrid(
  a_geometry geometry,
  fid numeric,
  size numeric,
  srid integer,
  lid varchar
  )
  RETURNS SETOF r_grid AS
  $BODY$
  DECLARE
    startX    NUMERIC;
    startY    NUMERIC;

    v_mbr     geometry;

    maxStepX  INT4;
    maxStepY  INT4;

    v_col     INT4;
    v_row     INT4;

    v_grid    R_Grid;
      tables CURSOR FOR SELECT
                          nitrogen,
                          st_transform(geo, srid) AS geo,
                          fertilization_classification_code as code
                        FROM fertility_map_infos
                        WHERE fertility_id = fid   AND id = ANY(lid::BIGINT[]);
    row       fertility_map_infos%ROWTYPE;
    temp_grid geometry;
    temp_mix  TEXT [];
    temp_code TEXT[];
    temp_char NUMERIC;
    p_geometry GEOMETRY;
  BEGIN
    -- work for mesh size from 10 to 50.
    IF (size > 50)
    THEN
      RETURN;
    END IF;
    IF (size < 10)
    THEN
      RETURN;
    END IF;
    -- get the bounding box of current selection.
    p_geometry := st_transform(a_geometry, srid);
    v_mbr   := st_envelope(p_geometry);
    -- start point X and Y for create new meshes.
    startX := ST_XMIN(v_mbr);
    startY := ST_YMIN(v_mbr);

    -- generate steps to create.
    maxStepX := ceil((ST_XMAX(v_mbr) - startX) / size) + 1;
    maxStepY := ceil((ST_YMAX(v_mbr) - startY) / size) + 1;

    -- start generate
    FOR v_col IN 0..maxStepX LOOP
      FOR v_row IN 0..maxStepY LOOP
        -- create new mesh which is polygon using size generate.
        temp_grid := ST_MakeEnvelope(startX + (size * v_col), startY + (size * v_row), startX + (size * v_col) + size,
                                     startY + (size * v_row) + size, srid);
        -- only take this new mesh if this mesh intersection with selection > 0.001
        temp_char := ((ST_Area(St_Intersection(temp_grid, p_geometry))) / ST_Area(temp_grid)) :: NUMERIC;
        IF (ST_Intersects(p_geometry, temp_grid) AND (temp_char > 0.001))
        THEN
          temp_mix := '{}';
          temp_code := '{}';
          -- get (intersection between each old mesh with new mesh ) / with its self
          FOR row IN tables LOOP
            IF (ST_Intersects(row.geo, temp_grid))
            THEN
              temp_char := ((ST_Area(St_Intersection(temp_grid, row.geo))) / ST_Area(row.geo)) :: NUMERIC;
              temp_char := round(temp_char :: NUMERIC, 2);

              IF (temp_char > 0.00)
              THEN
                temp_mix := array_append(temp_mix, array_to_string(ARRAY [temp_char, (row.nitrogen) :: NUMERIC], ','));
              END IF;
              temp_code := array_append(temp_code, (row.code)::TEXT);
            END IF;
          END LOOP;
          IF (temp_mix != '{}')
          THEN
            v_grid.mix := array_to_json(temp_mix);
            v_grid.code := array_to_json(temp_code);
            v_grid.geo := st_transform(temp_grid, 3857);
            RETURN NEXT v_grid;
          END IF;

        END IF;
      END LOOP;
    END LOOP;
  END;
  $BODY$
LANGUAGE plpgsql IMMUTABLE
COST 100
ROWS 1000;
ALTER FUNCTION st_makegrid(geometry, numeric, numeric, integer)
OWNER TO postgres;
