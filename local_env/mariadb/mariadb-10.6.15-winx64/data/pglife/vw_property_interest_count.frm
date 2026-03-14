TYPE=VIEW
query=select `p`.`id` AS `property_id`,`p`.`name` AS `property_name`,count(`iup`.`user_id`) AS `interest_count` from (`pglife`.`properties` `p` left join `pglife`.`interested_users_properties` `iup` on(`p`.`id` = `iup`.`property_id`)) group by `p`.`id`,`p`.`name`
md5=10c987941286170b9106a178ef654b94
updatable=0
algorithm=0
definer_user=root
definer_host=localhost
suid=2
with_check_option=0
timestamp=0001773477310897384
create-version=2
source=SELECT \n    p.id AS property_id,\n    p.name AS property_name,\n    COUNT(iup.user_id) AS interest_count\nFROM properties p\nLEFT JOIN interested_users_properties iup ON p.id = iup.property_id\nGROUP BY p.id, p.name
client_cs_name=utf8mb4
connection_cl_name=utf8mb4_general_ci
view_body_utf8=select `p`.`id` AS `property_id`,`p`.`name` AS `property_name`,count(`iup`.`user_id`) AS `interest_count` from (`pglife`.`properties` `p` left join `pglife`.`interested_users_properties` `iup` on(`p`.`id` = `iup`.`property_id`)) group by `p`.`id`,`p`.`name`
mariadb-version=100615
