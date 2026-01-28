-- Simple Movie Database Setup

CREATE DATABASE IF NOT EXISTS movie_database;
USE movie_database;

CREATE TABLE movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    director VARCHAR(255) NOT NULL,
    year INT NOT NULL,
    genre VARCHAR(100) NOT NULL,
    rating DECIMAL(3,1) DEFAULT NULL,
    description TEXT,
    poster_url VARCHAR(500) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample data
INSERT INTO movies (title, director, year, genre, rating, description, poster_url) VALUES
('The Shawshank Redemption', 'Frank Darabont', 1994, 'Drama', 9.3, 'Two imprisoned men bond over a number of years, finding solace and eventual redemption through acts of common decency.', 'https://via.placeholder.com/300x450?text=Shawshank'),
('The Godfather', 'Francis Ford Coppola', 1972, 'Crime', 9.2, 'The aging patriarch of an organized crime dynasty transfers control of his clandestine empire to his reluctant son.', 'https://via.placeholder.com/300x450?text=Godfather'),
('The Dark Knight', 'Christopher Nolan', 2008, 'Action', 9.0, 'When the menace known as the Joker wreaks havoc and chaos on the people of Gotham, Batman must accept one of the greatest psychological and physical tests.', 'https://via.placeholder.com/300x450?text=Dark+Knight'),
('Pulp Fiction', 'Quentin Tarantino', 1994, 'Crime', 8.9, 'The lives of two mob hitmen, a boxer, a gangster and his wife intertwine in four tales of violence and redemption.', 'https://via.placeholder.com/300x450?text=Pulp+Fiction');