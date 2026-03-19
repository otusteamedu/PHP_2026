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