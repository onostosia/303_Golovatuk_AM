#!/bin/bash
chcp 65001

sqlite3 movies_rating.db < db_init.sql

echo "1. Найти все комедии, выпущенные после 2000 года, которые понравились мужчинам (оценка не ниже 4.5). Для каждого фильма в этом списке вывести название, год выпуска и количество таких оценок."
echo "--------------------------------------------------"
sqlite3 movies_rating.db -box -echo "select title, year,  COUNT(*) AS 'count of ratings' from movies INNER JOIN ratings ON movies.id = movie_id INNER JOin users ON users.id = ratings.user_id WHERE genres LIKE '%%Comedy%%' AND year > 2000 AND rating >= 4.5 AND gender = 'male' Group By movies.id;"
echo " "

echo "2. Провести анализ занятий (профессий) пользователей - вывести количество пользователей для каждого рода занятий. Найти самую распространенную и самую редкую профессию посетитетей сайта."
echo "--------------------------------------------------"
sqlite3 movies_rating.db -box -echo "WITH occupations_and_count AS (SELECT occupation, count FROM (SELECT occupation, count(*) AS count FROM users GROUP BY occupation)) SELECT occupation, count as 'Popular and unpopular occupations' from (SELECT *, MAX(COUNT)over() AS 'pop', MIN(COUNT)Over() AS 'unpop' FROM occupations_and_count ) where count = pop OR count = unpop ORDER by COUNT DESC;"
echo " "

echo "3. Найти все пары пользователей, оценивших один и тот же фильм. Устранить дубликаты, проверить отсутствие пар с самим собой. Для каждой пары должны быть указаны имена пользователей и название фильма, который они ценили."
echo "--------------------------------------------------"
sqlite3 movies_rating.db -box -echo "Select DISTINCT u1.name, title, u2.name FROM ratings a, ratings b INNER JOIN movies ON a.movie_id = movies.id INNER JOIN users u1 ON a.user_id = u1.id INNER JOIN users u2 ON b.user_id = u2.id WHERE a.movie_id = b.movie_id and a.user_id < b.user_id LIMIT 100;"
echo " "

echo "4. Найти 10 самых свежих оценок от разных пользователей, вывести названия фильмов, имена пользователей, оценку, дату отзыва в формате ГГГГ-ММ-ДД."
echo "--------------------------------------------------"
sqlite3 movies_rating.db -box -echo "SELECT title, users.name AS 'names', a.rating, a.date FROM users, movies, (SELECT DISTINCT ratings.user_id, ratings.rating, ratings.movie_id, date(ratings.timestamp, 'unixepoch') AS date FROM ratings ORDER BY ratings.timestamp DESC)a WHERE users.id = a.user_id AND movies.id = a.movie_id GROUP BY a.user_id ORDER BY a.date DESC Limit 10;"
echo " "

echo "5. Вывести в одном списке все фильмы с максимальным средним рейтингом и все фильмы с минимальным средним рейтингом. Общий список отсортировать по году выпуска и названию фильма. В зависимости от рейтинга в колонке "Рекомендуем" для фильмов должно быть написано "Да" или "Нет"."
echo "--------------------------------------------------"
sqlite3 movies_rating.db -box -echo "WITH MovAveRat(year, title, avRat) AS (SELECT year, title, AVG(rating) FROM movies, ratings WHERE movies.id = ratings.movie_id and year <>0  GROUP BY movies.id) SELECT year, title, avRat as 'average rating', CASE WHEN avRat = maxAvRat THEN 'YES' ELSE 'NO' END Recomendation FROM (SELECT *, MAX(avRat)OVER() AS 'maxAvRat', MIN(avRat)OVER() AS 'minAvRat' FROM MovAveRat) WHERE avRat = maxAvRat or avRat = minAvRat ORDER BY year, title;"
echo " "

echo "6. Вычислить количество оценок и среднюю оценку, которую дали фильмам пользователи-женщины в период с 2010 по 2012 год."
echo "--------------------------------------------------"
sqlite3 movies_rating.db -box -echo "WITH umv(gender, rating, date) AS (select users.gender, ratings.rating, date(ratings.timestamp, 'unixepoch') FROM users, ratings WHERE users.id = ratings.user_id) SELECT COUNT(rating) AS 'count of ratings made by women', AVG(rating) AS'average rating' FROM umv WHERE date between '2010' AND '2012' AND umv.gender = 'female' ORDER BY date;"
echo " "

echo "7. Составить список фильмов с указанием их средней оценки и места в рейтинге по средней оценке. Полученный список отсортировать по году выпуска и названиям фильмов. В списке оставить первые 20 записей."
echo "--------------------------------------------------"
sqlite3 movies_rating.db -box -echo "WITH umv(year, title, rating) AS (select year, title, rating FROM movies, ratings WHERE movies.id = ratings.movie_id) SELECT  year, title, AVG(rating) AS 'average rating' , ROW_Number()OVER(ORDER BY AVG(rating)) AS 'place in the rating' FROM umv WHERE year <>0 GROUP BY title ORDER BY year limit 20;"
echo " "

echo "8. Определить самый распространенный жанр фильма и количество фильмов в этом жанре."
echo "--------------------------------------------------"
sqlite3 movies_rating.db -box -echo " with t(id, title, genres) AS (select id, title, genres from movies) SELECT genre_type AS 'the most common genre', MAX(count) AS 'count' FROM (SELECT genre_type, Count(genre_type) AS'COUNT' from (Select t.id, genres.id, genres.genre_type from t INNER JOIN genres WHERE INSTR(t.genres, genres.genre_type))GROUP BY genre_type);"
echo " "
