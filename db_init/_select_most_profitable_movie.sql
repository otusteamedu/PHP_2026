select * from movies where id IN (
    select
        movie_id
    from seanses
    where id = (
        select seanse_id
        from orders
        group by seanse_id
        order by sum(price) desc LIMIT 1
    )
    );

-- IMPROVED
select * from movies
                  JOIN (
    select movie_id from seanses
                             JOIN (SELECT seanse_id FROM orders
                                   WHERE paid_at >= NOW() - INTERVAL '14 days'
                                   GROUP BY seanse_id
                                   ORDER BY SUM(price) DESC LIMIT 1
    ) top ON seanses.id = top.seanse_id
) as top_movies ON top_movies.movie_id = movies.id