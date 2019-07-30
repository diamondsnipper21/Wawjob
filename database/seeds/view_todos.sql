select `t`.`id` AS `id`,`t`.`subject` AS `subject`,`t`.`type` AS `type`,`t`.`creator_id` AS `creator_id`,`t`.`assigner_ids` AS `assigner_ids`,`t`.`priority` AS `priority`,`t`.`due_date` AS `due_date`,`t`.`related_ticket_id` AS `related_ticket_id`,`t`.`description` AS `description`,`t`.`status` AS `status`,`t`.`created_at` AS `created_at`,`t`.`updated_at` AS `updated_at`,group_concat(`vu`.`fullname`,',' separator ',') AS `assigner_names` from (`todos` `t` left join `view_users` `vu` on((`t`.`assigner_ids` like convert(concat('%[',`vu`.`id`,']%') using utf8)))) group by `t`.`id`


select `t`.`id` AS `id`,`t`.`subject` AS `subject`,`t`.`type` AS `type`,`t`.`creator_id` AS `creator_id`,`t`.`assigner_ids` AS `assigner_ids`,`t`.`priority` AS `priority`,`t`.`due_date` AS `due_date`,`t`.`related_ticket_id` AS `related_ticket_id`,`t`.`description` AS `description`,`t`.`status` AS `status`,`t`.`created_at` AS `created_at`,`t`.`updated_at` AS `updated_at`,group_concat(`vu`.`fullname`,',' separator ',') AS `assigner_names` from (`todos` `t` left join `view_users` `vu` on((`t`.`assigner_ids` like concat('%[',`vu`.`id`,']%')))) group by `t`.`id`