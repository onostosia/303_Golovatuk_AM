import csv
# -*- coding: utf-8 -*-
def delite_apostr(inf):
    t = list(inf)
    for j in range(len(inf)):
        if  inf[j] == "'":
            #print(inf)
            t[j] = '"'
            
    inf= ''.join(str(e) for e in t)
    return inf;
    
def splite_year(arr):
    for i in range(len(arr)):
        
        for j in range(len(arr[i][1])):
            if  arr[i][1][j] == "'":
                t = list(arr[i][1])
                t[j] = '"'
                tmp= ''.join(str(e) for e in t)
                arr[i][1] = tmp
            else: tmp = arr[i][1] #     'title (year)'
        
        if  tmp[-1] == ' ' or  tmp[-1] == '"':
            arr[i][1] = arr[i][1][:len(arr[i][1]) - 1]
        
        
        if arr[i][1][0] == '"':
            arr[i][1] = arr[i][1].replace('"', "")
          
        tmp = arr[i][1] #     'title (year)'
        s = ''
        if tmp[-1] == ')':
            j = -2
            
            while (tmp[j] != '('):
                s+=tmp[j]
                j-=1
            s=s[::-1]
            
            if len(s) > 4:
               # print(s)
                s = s[:4]
               # print(s)
            try:
                arr[i].insert(2,int(s)) 
            except Exception:
                arr[i].insert(2,s) 
            arr[i][1] = arr[i][1][:len(arr[i][1])+ j-1]
        else:
            arr[i].insert(2,0) #no year listed
    return arr

''' 1 - movies.csv
    movies. Поля id (primary key), title, year, genres.
'''
with open("movies.csv") as m:
    movies = csv.reader(m, delimiter = ',')
    count = 0
    movies_data = []
    # Считывание данных из CSV файла
    for row in movies:
        movies_data.append(row)
m.close()   
#hilight table's title
tabel_titles_1 = movies_data[0]
tabel_titles_1.insert(2, 'year')
movies_data.remove(movies_data[0])
movies_data = splite_year(movies_data)

''' 2 - ratings.csv
    ratings. Поля id (primary key), user_id, movie_id, rating, timestamp.
'''
#print('\n\n2 - ratings.csv\nПоля id (primary key), user_id, movie_id, rating, timestamp.')
ratings_data = []
with open("ratings.csv") as r:
    ratings = csv.reader(r, delimiter = ',')
    # Считывание данных из CSV файла
    for row in ratings:
        ratings_data.append(row)
r.close()   
#hilight
tabel_titles_2= ratings_data[0]
ratings_data.remove(ratings_data[0])
###################################
''' 3 - tags.csv 
    tags. Поля id (primary key), user_id, movie_id, tag, timestamp.
'''
#print('\n\n3 - ratings.csv Поля id (primary key), user_id, movie_id, tag, timestamp.')
tags_data = []
with open("tags.csv") as t:
    tags = csv.reader(t, delimiter = ',')
    for row in tags:
        row[2] = delite_apostr(row[2])
        tags_data.append(row)
t.close()   
#hilight table's title
tabel_titles_3 = tags_data[0]
tags_data.remove(tags_data[0])

''' 4 - users.txt
    USERS Поля id (primary key), name, email, gender, register_date, occupation
'''
#print('\n\nUSERS Поля id (primary key), name, email, gender, register_date, occupation')
users_data =[]
f = open("users.txt", "r")
for line in f:
    info = line.split("|")
    info[1] = delite_apostr(info[1])
    info[0] = int(info[0])
    if info[5][-1] == '\n':
        info[5] = info[5][:len(info[5])-1]
      
    users_data.append(info)
for i in range(len(users_data)):
    users_data[i][0] = i+1
    
def SQLscript(f):
    #1 - movies
    f.write('DROP TABLE IF EXISTS movies;\n')
    f.write('CREATE TABLE movies(id INTEGER PRIMARY KEY, title VARCHAR(30), year INTEGER, genres VARCHAR(50));')
    
    for i in range(len(movies_data)-1):#len(movies_data)-1
        try:
            f.write('\nINSERT INTO movies(id, title, year, genres) VALUES ')
            f.write("({}, '{}', {}, '{}');\n".format(movies_data[i][0], movies_data[i][1], movies_data[i][2], movies_data[i][3]))
        except:
            print(movies_data[i][0], movies_data[i][1], movies_data[i][2], movies_data[i][3])
    f.write("INSERT INTO movies(id, title, year, genres) VALUES ({}, '{}', {}, '{}');".format(movies_data[len(movies_data)-1][0], movies_data[len(movies_data)-1][1], movies_data[len(movies_data)-1][2], movies_data[len(movies_data)-1][3]))
    #2 - ratings
    f.write('\n\nDROP TABLE IF EXISTS ratings;\n')
    f.write('CREATE TABLE ratings(id INTEGER PRIMARY KEY AUTOINCREMENT,user_id INTEGER, movie_id INTEGER, rating INTEGER, timestamp INTEGER);')
    f.write('\nINSERT INTO ratings(user_id, movie_id, rating, timestamp) \nVALUES ')
    for i in range(len(ratings_data)-1):#len(ratings_data)-1
        f.write("({}, {}, {}, {}),\n".format(ratings_data[i][0], ratings_data[i][1], ratings_data[i][2], ratings_data[i][3]))
    f.write("({}, {}, {}, {});".format(ratings_data[len(ratings_data)-1][0], ratings_data[len(ratings_data)-1][1], ratings_data[len(ratings_data)-1][2], ratings_data[len(ratings_data)-1][3]))

    #3 - tags
    f.write('\n\nDROP TABLE IF EXISTS tags;')
    f.write('\nCREATE TABLE tags(id INTEGER PRIMARY KEY AUTOINCREMENT,users_id INTEGER, movie_id INTEGER, tag VARCHAR(20), timestamp1 INTEGER);')
    f.write('\nINSERT INTO tags(users_id, movie_id, tag, timestamp1) \nVALUES ')
    for i in range(len(tags_data)-1):#len(tags_data)-1
        f.write("({}, {}, '{}', {}),\n".format(tags_data[i][0], tags_data[i][1], tags_data[i][2], tags_data[i][3]))
    f.write("({}, {}, '{}', {});".format(tags_data[i][0], tags_data[i][1], tags_data[i][2], tags_data[i][3]))

    #4 - users
    f.write('\n\nDROP TABLE IF EXISTS users;')
    f.write('\nCREATE TABLE users(id INTEGER PRIMARY KEY, name VARCHAR(30), email VARCHAR, gender VARCHAR(10), register_date VARCHAR, occupation VARCHAR);')
    f.write('\nINSERT INTO users(id, name, email, gender, register_date, occupation) VALUES ')
    for i in range(len(users_data)-1):
        f.write("({}, '{}', '{}', '{}', '{}', '{}'),\n".format(users_data[i][0], users_data[i][1], users_data[i][2], users_data[i][3], users_data[i][4], users_data[i][5]))
    f.write("({}, '{}', '{}', '{}', '{}', '{}');".format(users_data[len(users_data) - 1][0], users_data[len(users_data) - 1][1], users_data[len(users_data) - 1][2], users_data[len(users_data) - 1][3], users_data[len(users_data) - 1][4], users_data[len(users_data) - 1][5]))
    
    #comands for showing tables
    '''f.write('\n\n.width 10\n')
    f.write('\n.mode column')
    f.write('\n SELECT * FROM movies;')
    f.write('\n SELECT * FROM ratings;')
    f.write('\n SELECT * FROM tags;')
    f.write('\n SELECT * FROM users;')'''




with open('db_init.sql', 'w', encoding="utf-8") as sqlfile:
    SQLscript(sqlfile)
    #print('Ok')
sqlfile.close() 

