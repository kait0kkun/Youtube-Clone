-- Create the database
CREATE DATABASE youtube_db;

-- Use the database
USE youtube_db;

-- Create the youtube_channels table
CREATE TABLE youtube_channels (
  channel_id INT PRIMARY KEY,
  channel_name VARCHAR(255),
  description TEXT,
  subscriber_count INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create the youtube_channel_videos table
CREATE TABLE youtube_channel_videos (
  video_id INT PRIMARY KEY,
  channel_id INT,
  video_title VARCHAR(255),
  description TEXT,
  publish_date DATE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (channel_id) REFERENCES youtube_channels(channel_id)
);
