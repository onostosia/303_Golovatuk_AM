#!/bin/bash
chcp 65001

sqlite3 movies_rating.db < db_init.sql

echo 1.Составить список фильмов, имеющих хотя бы одну оценку. Список фильмов отсортировать по году выпуска и по названиям. В списке оставить первые 10 фильмов.
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo "SELECT movies.year, movies.title, ratings.rating FROM movies, ratings WHERE (movies.id = ratings.movie_id and movies.year <> 0) ORDER BY movies.year, movies.title LIMIT 10;"
echo " "

echo 2.Вывести список всех пользователей, фамилии которых начинаются на букву 'A'. Полученный список отсортировать по дате регистрации. В списке оставить первых 5 пользователей.
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo "SELECT * FROM users WHERE name LIKE '%% A%%'ORDER BY register_date LIMIT 5;"
echo " "

echo 3.Написать запрос, возвращающий информацию о рейтингах в более читаемом формате: имя и фамилия эксперта, название фильма, год выпуска, оценка и дата оценки в формате ГГГГ-ММ-ДД. Отсортировать данные по имени эксперта, затем названию фильма и оценке. В списке оставить первые 50 записей.
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo "SELECT users.name, movies.title, movies.year, ratings.rating, date(ratings.timestamp, 'unixepoch') AS date FROM ratings INNER JOIN users ON ratings.user_id = users.id INNER JOIN movies ON movies.id = ratings.movie_id ORDER BY users.name ASC, title ASC, ratings.rating ASC LIMIT 50;"
echo " "

echo 4.Вывести список фильмов с указанием тегов, которые были им присвоены пользователями. Сортировать по году выпуска, затем по названию фильма, затем по тегу. В списке оставить первые 40 записей.
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo "SELECT movies.year, movies.title, tags.tag, users.name FROM movies INNER JOIN tags ON movies.id = tags.movie_id AND movies.year <> 0 INNER JOIN users ON users.id = tags.users_id ORDER BY year, title, tag LIMIT 40;"
echo " "

echo 5.Вывести список самых свежих фильмов. В список должны войти все фильмы последнего года выпуска, имеющиеся в базе данных. Запрос должен быть универсальным, не зависящим от исходных данных.
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo " SELECT title, year FROM movies WHERE year = (SELECT MAX(year) FROM movies WHERE year<>0);"
echo " "