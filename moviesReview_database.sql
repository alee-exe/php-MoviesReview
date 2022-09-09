/*This is used to create the database named ‘MovieWeb_DB’*/ 

CREATE DATABASE MovieWeb_DB; 

 

/*This is used to create a ‘User’ table to store all registered user’s details*/ 

CREATE TABLE User ( 

  User_ID INT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE, 

  Username VARCHAR(20) NOT NULL, 

  Password VARCHAR(20) NOT NULL, 

  Email VARCHAR(30) NOT NULL, 

  User_Type ENUM('P', 'T'), 

  List_Name VARCHAR(30), 

  List_Description TEXT, 

  PRIMARY KEY (User_ID) 

); 

 

/*This is used to create a subtype table called ‘Premium’ of the supertype User*/ 

CREATE TABLE Premium ( 

  User_ID INT UNSIGNED NOT NULL UNIQUE, 

  Payment_Date DATETIME NOT NULL, 

  Cost DECIMAL(5,2) NOT NULL, 

  Expire_Date DATETIME NOT NULL, 

  PRIMARY KEY (User_ID), 

  FOREIGN KEY (User_ID) REFERENCES User(User_ID) 

ON DELETE CASCADE 

); 

 

/*This is used to create a subtype table called ‘Trial’ of the supertype User*/ 

CREATE TABLE Trial ( 

  User_ID INT UNSIGNED NOT NULL UNIQUE, 

  Start_Date DATETIME NOT NULL, 

  Expire_Date DATETIME NOT NULL, 

  PRIMARY KEY (User_ID), 

  FOREIGN KEY (User_ID) REFERENCES User(User_ID) 

ON DELETE CASCADE 

); 

 

/*This is used to create an ‘Actor’ table to store all the actors that are casted into the Movie_genres table*/ 

CREATE TABLE Actor ( 

  Actor_ID INT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE, 

  Forename VARCHAR(15) NOT NULL, 

  Surname VARCHAR(15) NOT NULL, 

  PRIMARY KEY (Actor_ID) 

); 

 

 

/*This is used to create a ‘Movie’ table to store all the movies that are submitted by users to the database*/ 

CREATE TABLE Movie ( 

  Movie_ID INT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE, 

  Title VARCHAR(60) NOT NULL, 

  Release_Date DATE NOT NULL, 

  Cover LONGBLOB, 

  Director VARCHAR(30) NOT NULL, 

  User_ID INT UNSIGNED NOT NULL, 

  PRIMARY KEY (Movie_ID), 

  FOREIGN KEY (User_ID) REFERENCES User(User_ID) 

ON DELETE CASCADE 

); 

 

/*This is used to create a table named ‘Movie_genres’ to link genres to movies (from the ‘Movie’ table) */ 

CREATE TABLE Movie_genres ( 

  Movie_ID INT UNSIGNED NOT NULL, 

  Genre ENUM('Animation', 'Action', 'Adventure', 'Comedy', 'Crime', 'Drama', 'Fantasy', 'Historical', 'Horror', 'Science Fiction', 'Mystery', 'Romance', 'Social') NOT NULL, 

  PRIMARY KEY (Movie_ID, Genre), 

  FOREIGN KEY (Movie_ID) REFERENCES Movie(Movie_ID) 

ON DELETE CASCADE 

); 

 

/*This is used to create a table named ‘User_reports’ to insert user’s reports which links users from the ‘User’ table*/ 

CREATE TABLE User_reports ( 

  Report_ID INT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE, 

  Reporter_ID INT UNSIGNED NOT NULL, 

  Reportee_ID INT UNSIGNED NOT NULL, 

  Category ENUM('Inappropriate', 'Spam', 'Self-Promotional', 'Incorrect Movie', 'Spoiler', 'Other') NOT NULL, 

  Comment TEXT, 

  Report_Date DATETIME NOT NULL, 

  PRIMARY KEY (Report_ID), 

  FOREIGN KEY (Reporter_ID) REFERENCES User(User_ID) 

ON DELETE CASCADE, 

  FOREIGN KEY (Reportee_ID) REFERENCES User(User_ID) 

ON DELETE CASCADE 

); 

 

/*This is used to create a table named ‘User_reviews’ to insert user’s reviews on a movie which links users from the ‘User’ table and movies from the ‘Movie’ table*/ 

CREATE TABLE User_reviews ( 

  User_ID INT UNSIGNED NOT NULL, 

  Movie_ID INT UNSIGNED NOT NULL, 

  Review_Date TIMESTAMP NOT NULL, 

  Rating TINYINT UNSIGNED NOT NULL CHECK (Rating >= 0 AND Rating <=5), 

  Comment TEXT NOT NULL, 

  PRIMARY KEY (User_ID, Movie_ID), 

  FOREIGN KEY (User_ID) REFERENCES User(User_ID) 

ON DELETE CASCADE, 

  FOREIGN KEY (Movie_ID) REFERENCES Movie(Movie_ID) 

ON DELETE CASCADE 

); 

 

/*This is used to create a table named ‘Watchlist_adds’ to link movies that are added into a user’s watchlist (from the ‘User’ table)*/ 

CREATE TABLE Watchlist_adds ( 

  User_ID INT UNSIGNED NOT NULL, 

  Movie_ID INT UNSIGNED NOT NULL, 

  Add_Date TIMESTAMP NOT NULL, 

  PRIMARY KEY (User_ID, Movie_ID), 

  FOREIGN KEY (User_ID) REFERENCES User(User_ID) 

ON DELETE CASCADE, 

  FOREIGN KEY (Movie_ID) REFERENCES Movie(Movie_ID) 

ON DELETE CASCADE 

); 

 

/*This is used to create a table named ‘Movie_genres’ to link actors (from the ‘Actor’ table) in movies (from the ‘Movie’ table) and store an actor’s role in a movie*/ 

CREATE TABLE Movie_cast ( 

  Movie_ID INT UNSIGNED NOT NULL, 

  Actor_ID INT UNSIGNED NOT NULL, 

  Role VARCHAR(30) NOT NULL, 

  PRIMARY KEY (Movie_ID, Actor_ID), 

  FOREIGN KEY (Movie_ID) REFERENCES Movie(Movie_ID) 

ON DELETE CASCADE, 

  FOREIGN KEY (Actor_ID) REFERENCES Actor(Actor_ID) 

ON DELETE CASCADE

); 

 

/*The statements below are explained in the documentation*/ 

INSERT INTO User (Username, Password, Email) VALUES('John', 'password123', 'johnsmith@email.com'); 

 

INSERT INTO User (Username, Password, Email, User_Type, List_Name, List_Description) VALUES('Kit', 'kittenssmitten', 'kitmit@email.com', 'P', 'Kit\'s cool list', 'The one and only Kitten'); 

 

INSERT INTO User (Username, Password, Email, User_Type) VALUES('Pops', 'pippitypop', 'pippop@email.com', 'T'); 

 

INSERT INTO Movie (Title, Release_Date, Director, User_ID) VALUES('Indiana Jones and the Last Crusade', '1989-05-30', 'Steven Spielberg', 1); 



INSERT INTO Premium (User_ID, Payment_Date, Cost, Expire_Date) VALUES('2', '2020-01-01 12:00:00', '18.99', '2021-01-01 12:00:00');



INSERT INTO Trial (User_ID, Start_Date, Expire_Date) VALUES('3', '2020-01-01 12:00:00', '2020-02-01 12:00:00');



INSERT INTO Watchlist_adds (User_ID, Movie_ID, Add_Date) VALUES ('1', '1', '2020-01-01 12:00:00');



INSERT INTO Movie_genres (Movie_ID, Genre) VALUES 

(1, 'Action'), 

(1, 'Adventure'); 

 

INSERT INTO Actor (Actor_ID, Forename, Surname) VALUES 

(1, 'Harrison', 'Ford'), 

(2, 'Sean', 'Connery'), 

(3, 'Denholm', 'Elliot'), 

(4, 'Alison', 'Doody'); 

 

INSERT INTO Movie_cast (Movie_ID, Actor_ID, Role) VALUES 

(1, 1, 'Indiana Jones'), 

(1, 2, 'Professor Henry Jones'), 

(1, 3, 'Marcus Brody'), 

(1, 4, 'Elsa'); 

 

INSERT INTO User_reviews (User_ID, Movie_ID, Review_Date, Rating, Comment) VALUES (1, 1, '2020-01-01 12:00:00', 4, 'It has a grand scope and epic scale, still, but we\'re left with a larger idea of the man wearing the fedora.'); 

 

INSERT INTO User_reviews (User_ID, Movie_ID, Review_Date, Rating, Comment) VALUES (2, 1, '2020-01-01 12:00:00', 4, ' As usual, the action is on an epic scale and delivered with breathless enthusiasm and much panache by director Steven Spielberg.'); 

 

INSERT INTO User_reviews (User_ID, Movie_ID, Review_Date, Rating, Comment) VALUES (3, 1, '2020-01-01 12:00:00', 5, ' Indiana Jones and the Last Crusade is the most wonderful lark. It is also a class act.'); 

 