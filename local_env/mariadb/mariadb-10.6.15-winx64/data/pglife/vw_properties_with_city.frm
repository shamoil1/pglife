TYPE=VIEW
query=select `p`.`id` AS `id`,`p`.`city_id` AS `city_id`,`p`.`name` AS `name`,`p`.`address` AS `address`,`p`.`description` AS `description`,`p`.`gender` AS `gender`,`p`.`rent` AS `rent`,`p`.`rating_clean` AS `rating_clean`,`p`.`rating_food` AS `rating_food`,`p`.`rating_safety` AS `rating_safety`,`p`.`created_at` AS `created_at`,`p`.`updated_at` AS `updated_at`,`c`.`name` AS `city_name`,round((`p`.`rating_clean` + `p`.`rating_food` + `p`.`rating_safety`) / 3,1) AS `avg_rating` from (`pglife`.`properties` `p` join `pglife`.`cities` `c` on(`p`.`city_id` = `c`.`id`))
md5=d4dc91f074caceac5165245e0a361fd0
updatable=1
algorithm=0
definer_user=root
definer_host=localhost
suid=2
with_check_option=0
timestamp=0001773477310798753
create-version=2
source=SELECT \n    p.*,\n    c.name AS city_name,\n    ROUND((p.rating_clean + p.rating_food + p.rating_safety) / 3, 1) AS avg_rating\nFROM properties p\nINNER JOIN cities c ON p.city_id = c.id
client_cs_name=utf8mb4
connection_cl_name=utf8mb4_general_ci
view_body_utf8=select `p`.`id` AS `id`,`p`.`city_id` AS `city_id`,`p`.`name` AS `name`,`p`.`address` AS `address`,`p`.`description` AS `description`,`p`.`gender` AS `gender`,`p`.`rent` AS `rent`,`p`.`rating_clean` AS `rating_clean`,`p`.`rating_food` AS `rating_food`,`p`.`rating_safety` AS `rating_safety`,`p`.`created_at` AS `created_at`,`p`.`updated_at` AS `updated_at`,`c`.`name` AS `city_name`,round((`p`.`rating_clean` + `p`.`rating_food` + `p`.`rating_safety`) / 3,1) AS `avg_rating` from (`pglife`.`properties` `p` join `pglife`.`cities` `c` on(`p`.`city_id` = `c`.`id`))
mariadb-version=100615
