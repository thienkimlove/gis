-- Function: get_data_for_fertilizer_map_creation(text, integer, bigint, text, bigint, numeric, numeric, refcursor, refcursor, refcursor, refcursor, refcursor, refcursor, refcursor, refcursor, refcursor, refcursor, refcursor, refcursor, refcursor)

-- DROP FUNCTION get_data_for_fertilizer_map_creation(text, integer, bigint, text, bigint, numeric, numeric, refcursor, refcursor, refcursor, refcursor, refcursor, refcursor, refcursor, refcursor, refcursor, refcursor, refcursor, refcursor, refcursor);

CREATE OR REPLACE FUNCTION get_data_for_fertilizer_map_creation(
    selectionarea text,
    cropsid integer,
    fertilizerstandarddefinitionid bigint,
    fertility_map_infosid text,
    userfertilizerdefinitiondetailid bigint,
    kaliratio numeric,
    pratio numeric,
    polygonselectionarea refcursor,
    crop refcursor,
    fertilizationstandard refcursor,
    fertilizationdivision refcursor,
    userfertilizerdefinitiondetails refcursor,
    selectednitrogens refcursor,
    systemfertilizerdefinitiondetailnitos1 refcursor,
    systemfertilizerdefinitiondetailnitos2 refcursor,
    userfertilizerdefinitiondetailnitos refcursor,
    systemfertilizerdefinitiondetailkalis refcursor,
    systemfertilizerdefinitiondetailphotphos refcursor,
    userfertilizerdefinitiondetailkalis refcursor,
    userfertilizerdefinitiondetailphotphos refcursor)
  RETURNS SETOF refcursor AS
$BODY$
  DECLARE isSystemFertilization boolean;
  BEGIN
    IF EXISTS (SELECT id from fertilizer_standard_definitions where id =fertilizerStandardDefinitionId
                                                                    and not_available = false and created_by =0)
    then
      isSystemFertilization := true;
    else
      isSystemFertilization := false;
    end if;

    IF selectionArea LIKE 'POLYGON((%'
    THEN
      OPEN polygonSelectionArea FOR
      SELECT ST_GeomFromText(selectionArea,3857);
-- Return the cursor to the caller
      RETURN NEXT polygonSelectionArea;
    ELSE
      OPEN polygonSelectionArea FOR
      SELECT polygonSelectionArea;
-- Return the cursor to the caller
      RETURN NEXT polygonSelectionArea;

    END IF;
    OPEN crop FOR SELECT * from crops_definitions
    where id =  cropsId;   -- Open the second cursor
      -- Return the cursor to the caller
    RETURN NEXT crop;

    OPEN fertilizationStandard FOR
    SELECT *from fertilizer_standard_definitions where id = fertilizerStandardDefinitionId
                                                       and not_available = false;
-- Return the cursor to the caller
    RETURN NEXT fertilizationStandard;

    OPEN fertilizationDivision FOR
    SELECT distinct fertilization_divisions.fertilization_classification_code,fertilization_divisions.n,fertilization_divisions.p,
      fertilization_divisions.k
    from fertility_map_infos,fertilization_divisions
    where fertility_map_infos.fertilization_classification_code = fertilization_divisions.fertilization_classification_code
          and fertility_map_infos.id = any(fertility_map_infosId::bigint[])
          and crops_code = (select crops_code from crops_definitions where id =cropsId)
          and fertilization_divisions.n >0
    ;
-- Return the cursor to the caller
    RETURN NEXT fertilizationDivision;

    OPEN userFertilizerDefinitionDetails FOR
    SELECT *from user_fertilizer_definition_details
    where fertilizer_standard_definition_id =fertilizerStandardDefinitionId
          and crops_id =cropsId and not_available = false limit 1;
-- Return the cursor to the caller
    RETURN NEXT userFertilizerDefinitionDetails;

    OPEN selectedNitrogens FOR
    SELECT distinct nitrogen FROM fertility_map_infos where id = any(fertility_map_infosId::bigint[]);
-- Return the cursor to the caller
    RETURN NEXT selectedNitrogens;
    IF(isSystemFertilization)
    THEN
      OPEN systemFertilizerDefinitionDetailNitos1 FOR
      SELECT n,n_amount from system_fertilizer_definition_detail_nitos
      where n in ( select CAST (nitrogen as int) from fertility_map_infos  where id = any(fertility_map_infosId::bigint[]))
            and fertilizer_standard_definition_id = fertilizerStandardDefinitionId
            and crops_id = cropsId and n_amount is not null
      ;

-- Return the cursor to the caller
      RETURN NEXT systemFertilizerDefinitionDetailNitos1;

      OPEN systemFertilizerDefinitionDetailNitos2 FOR
      SELECT n,
        n_amount,
        division_amount1,
        division_amount2,
        division_amount3,
        division_amount4,
        division_amount5,
        division_amount6,
        division_amount7,
        division_amount8,
        division_amount9,
        division_amount10,
        division_amount11,
        division_amount12,
        division_amount13,
        division_amount14,
        division_amount15,
        division_amount16,
        division_amount17,
        division_amount18,
        division_amount19,
        division_amount20
      FROM system_fertilizer_definition_detail_nitos
      where n in ( select cast(nitrogen as int) from fertility_map_infos  where id = any(fertility_map_infosId::bigint[]))
            and fertilizer_standard_definition_id = fertilizerStandardDefinitionId
            and crops_id = cropsId
      ;
-- Return the cursor to the caller
      RETURN NEXT systemFertilizerDefinitionDetailNitos2;
      OPEN systemFertilizerDefinitionDetailKalis FOR
      SELECT fertilization_standard_amount FROM system_fertilizer_definition_detail_kalis where
        ratio = kaliRatio and fertilizer_standard_definition_id = fertilizerStandardDefinitionId
        and crops_id = cropsId;
-- Return the cursor to the caller
      RETURN NEXT systemFertilizerDefinitionDetailKalis;

      OPEN systemFertilizerDefinitionDetailPhotphos FOR
      SELECT fertilization_standard_amount FROM system_fertilizer_definition_detail_photphos where
        ratio = pRatio and fertilizer_standard_definition_id = fertilizerStandardDefinitionId
        and crops_id = cropsId;
-- Return the cursor to the caller
      RETURN NEXT systemFertilizerDefinitionDetailPhotphos;
    ELSE
      OPEN userFertilizerDefinitionDetailNitos FOR
      SELECT distinct nitrogen,fertilization_standard_amount FROM user_fertilizer_definition_detail_nitos
      WHERE user_fertilizer_definition_detail_id in
            (
              select id from user_fertilizer_definition_details
              where fertilizer_standard_definition_id= fertilizerStandardDefinitionId
                    and crops_id = cropsId
            );
-- Return the cursor to the caller
      RETURN NEXT userFertilizerDefinitionDetailNitos;

      OPEN userFertilizerDefinitionDetailKalis FOR
      SELECT distinct  fertilization_standard_amount FROM user_fertilizer_definition_detail_kalis where
        ratio = kaliRatio and user_fertilizer_definition_detail_id in
                              (
                                select id from user_fertilizer_definition_details
                                where fertilizer_standard_definition_id= fertilizerStandardDefinitionId
                                      and crops_id = cropsId
                              );
-- Return the cursor to the caller
      RETURN NEXT userFertilizerDefinitionDetailKalis;

      OPEN userFertilizerDefinitionDetailPhotphos FOR
      SELECT distinct  fertilization_standard_amount FROM user_fertilizer_definition_detail_photphos where
        ratio = pRatio and user_fertilizer_definition_detail_id in
                           (
                             select id from user_fertilizer_definition_details
                             where fertilizer_standard_definition_id= fertilizerStandardDefinitionId
                                   and crops_id = cropsId
                           );
-- Return the cursor to the caller
      RETURN NEXT userFertilizerDefinitionDetailPhotphos;
    END IF;

  END;
  $BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000;
ALTER FUNCTION get_data_for_fertilizer_map_creation(text, integer, bigint, text, bigint, numeric, numeric, refcursor, refcursor, refcursor, refcursor, refcursor, refcursor, refcursor, refcursor, refcursor, refcursor, refcursor, refcursor, refcursor)
  OWNER TO postgres;
