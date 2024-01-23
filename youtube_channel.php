<?php

require __DIR__ . '/vendor/autoload.php';

use Google\Client;

// Set up the Google API client
$client = new Google\Client();
$client->setApplicationName('YouTube Channel Sync');
$client->setScopes([Google\Service\YouTube::YOUTUBE_READONLY]);
$client->setAuthConfig('path/to/client_secret.json');
$client->setAccessType('offline');

// Set up the YouTube service
$youtube = new Google\Service\YouTube($client);

// Connect to the database
$servername = 'localhost';
$username = 'postgre';
$password = 'admin';
$dbname = 'youtube_db';

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Function to insert channel information into youtube_channels table
function insertChannelInfo($conn, $channelId, $channelName, $description, $thumbnail)
{
    $stmt = $conn->prepare('INSERT INTO youtube_channels (channel_id, channel_name, description, thumbnail) VALUES (:channelId, :channelName, :description, :thumbnail)');
    $stmt->bindParam(':channelId', $channelId);
    $stmt->bindParam(':channelName', $channelName);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':thumbnail', $thumbnail);
    $stmt->execute();
}

// Function to insert video information into youtube_channel_videos table
function insertVideoInfo($conn, $videoId, $channelId, $videoTitle, $description, $thumbnail)
{
    $stmt = $conn->prepare('INSERT INTO youtube_channel_videos (video_id, channel_id, video_title, description, thumbnail) VALUES (:videoId, :channelId, :videoTitle, :description, :thumbnail)');
    $stmt->bindParam(':videoId', $videoId);
    $stmt->bindParam(':channelId', $channelId);
    $stmt->bindParam(':videoTitle', $videoTitle);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':thumbnail', $thumbnail);
    $stmt->execute();
}

// Fetch channel information and videos
try {
    // Set the YouTube channel ID
    $channelId = 'UCWJ2lWNubArHWmf3FIHbfcQ';

    // Retrieve channel information
    $channelResponse = $youtube->channels->listChannels('snippet', ['id' => $channelId]);
    $channel = $channelResponse->getItems()[0];
    $channelName = $channel->getSnippet()->getTitle();
    $description = $channel->getSnippet()->getDescription();
    $thumbnail = $channel->getSnippet()->getThumbnails()->getDefault()->getUrl();

    // Save channel information in youtube_channels table
    insertChannelInfo($conn, $channelId, $channelName, $description, $thumbnail);

    // Retrieve latest 100 videos from the channel
    $videosResponse = $youtube->search->listSearch('snippet', ['channelId' => $channelId, 'maxResults' => 100, 'order' => 'date']);
    $videos =
