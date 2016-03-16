DROP INDEX IF EXISTS  fertilizer_map_infos_index_not_primarykey_columns ;
CREATE INDEX fertilizer_map_infos_index_not_primarykey_columns ON fertilizer_map_infos (fertilizer_id,main_fertilizer,sub_fertilizer,r,g,b);
DROP INDEX IF EXISTS  fertility_map_infos_index_not_primarykey_columns ;
CREATE INDEX fertility_map_infos_index_not_primarykey_columns ON fertility_map_infos (fertility_id,nitrogen,fertilization_classification_code);
DROP INDEX IF EXISTS  fertilizer_maps_infos_index_not_primarykey_columns ;
CREATE INDEX fertilizer_maps_infos_index_not_primarykey_columns ON fertilizer_maps (layer_id,fertility_map_id);