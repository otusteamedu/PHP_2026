SELECT
    m.title AS movie_title,
    SUM(t.actual_price) AS total_revenue
FROM
    cinema.movies m
JOIN
    cinema.screenings s ON m.id = s.movie_id
JOIN
    cinema.tickets t ON s.id = t.screening_id
GROUP BY
    m.title
ORDER BY
    total_revenue DESC
LIMIT 1;