SELECT
	p.id,
    p.`name`,
    p.amount,
    COALESCE(SUM(s.cost), 0)              AS total_cost,
    count(s.`procedure_id`)               AS services_count,
    COALESCE(SUM(s.cost) - p.amount, 0)   AS amount_difference
FROM
	`procedures` p
    	LEFT JOIN `services` s
        	ON p.`id` = s.`procedure_id`
GROUP BY
	p.id
ORDER BY
	services_count DESC,
    amount_difference DESC,
    total_cost DESC,
    p.id ASC

/**
* or maybe you like this option more
*

SELECT
    COALESCE(srv.services_count, 0)         AS services_count,
	COALESCE(srv.cost_total, 0)             AS cost_total,
    COALESCE(srv.cost_total - p.amount, 0)  AS cost_difference,
    p.amount,
	p.id,
    p.`name`
FROM
	`procedures` p
    	LEFT JOIN (
            SELECT
                s.`procedure_id`,
                SUM(s.cost)               AS cost_total,
                count(s.`procedure_id`)   AS services_count
            FROM
                `services` s
            GROUP BY 1
        ) AS srv
        	ON p.`id` = srv.`procedure_id`
ORDER BY
	services_count DESC,
    cost_difference DESC,
    cost_total DESC,
    p.id ASC

*/