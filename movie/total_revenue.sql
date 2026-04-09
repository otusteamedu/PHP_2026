SELECT 
    m.id,
    m.title,
    SUM(t.price_amount) AS total_revenue
FROM movies m
JOIN screenings s ON m.id = s.movie_id
JOIN tickets t ON s.id = t.screening_id
JOIN orders o ON t.order_id = o.id
WHERE t.status = 'paid' 
  AND o.status = 'paid'
  AND s.status != 'cancelled'
GROUP BY 
    m.id, 
    m.title
ORDER BY 
    total_revenue DESC
LIMIT 1;