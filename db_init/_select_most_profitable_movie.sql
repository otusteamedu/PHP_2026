select * from movies where id IN (
    select
        movie_id
    from seanses
    where id = (
        select seans_id
        from orders
        group by seans_id
        order by sum(price) desc LIMIT 1
    )
    );