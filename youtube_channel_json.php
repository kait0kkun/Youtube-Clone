<?php

require __DIR__ . '/vendor/autoload.php';

use Google\Client;

// Set up the Google API client
$client = new Google\Client();
$client->setApplicationName('YouTube Channel JSON');
$client->setScopes([Google\Service\YouTube::YOUTUBE_READONLY]);
$client->setAuthConfig('path/to/client_secret.json');
$client->setAccessType('offline');

// Set up the YouTube service
$youtube = new Google\Service\YouTube($client);

// Set the YouTube channel ID
$channelId = 'UCWJ2lWNubArHWmf3FIHbfcQ';

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

// Function to fetch channel information
function getChannelInfo($conn, $channelId)
{
    $stmt = $conn->prepare('SELECT * FROM youtube_channels WHERE channel_id = :channelId');
    $stmt->bindParam(':channelId', $channelId);
    $stmt->execute();
    $channel = $stmt->fetch(PDO::FETCH_ASSOC);
    return $channel;
}

// Function to fetch videos for a channel
function getChannelVideos($conn, $channelId)
{
    $stmt = $conn->prepare('SELECT * FROM youtube_channel_videos WHERE channel_id = :channelId');
    $stmt->bindParam(':channelId', $channelId);
    $stmt->execute();
    $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $videos;
}

// Fetch channel information and videos
try {
    // Retrieve channel information
    $channel = getChannelInfo($conn, $channelId);

    // Retrieve videos for the channel
    $videos = getChannelVideos($conn, $channelId);

    // Build the JSON feed
    $feed = [
        'channel_name' => $channel['channel_name'],
        'description' => $channel['description'],
        'thumbnail' => $channel['thumbnail'],
        'videos' => $videos
    ];

    // Set the response header
    header('Content-Type: application/json');

    // Output the JSON feed
    echo json_encode($feed, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
