-- 2. Подсчёт проданных билетов за неделю
SELECT count(*) AS tickets_sold
FROM ticket
WHERE sold_at >= date_trunc('day', now()) - interval '6 days';