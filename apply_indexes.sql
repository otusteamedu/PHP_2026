SET search_path TO cinema;

-- 1. Для ускорения поиска сеансов по времени (Запросы №1, №3)
CREATE INDEX idx_screenings_start_time ON screenings(start_time);

-- 2. Для ускорения подсчета билетов и выручки за период (Запросы №2, №4)
-- Используем индекс по внешнему ключу в связке с временем бронирования
CREATE INDEX idx_bookings_time ON bookings(booking_time);

-- 3. Покрывающий индекс для ускорения агрегации по билетам (Запрос №6)
CREATE INDEX idx_tickets_screening_price ON tickets(screening_id) INCLUDE (actual_price);

ANALYZE;
