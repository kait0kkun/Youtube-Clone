from googleapiclient.discovery import build

# Set up the API client
api_key = "AIzaSyDzPO1Mey4u4kRj_iI8rXYjOg8DoDU0UHc"
youtube = build('youtube', 'v3', developerKey=api_key)

# Get channel information
channel_response = youtube.channels().list(
    part='snippet,statistics',
    forUsername='NBA'
).execute()

channel = channel_response['items'][0]
channel_title = channel['snippet']['title']
channel_description = channel['snippet']['description']
channel_subscriber_count = channel['statistics']['subscriberCount']

print("Channel Information:")
print("Title:", channel_title)
print("Description:", channel_description)
print("Subscriber Count:", channel_subscriber_count)
print()

# Get the latest 100 videos from the channel
videos_response = youtube.search().list(
    part='snippet',
    channelId=channel['id'],
    maxResults=100,
    order='date'
).execute()

videos = videos_response['items']

print("Latest 100 Videos:")
for video in videos:
    video_title = video['snippet']['title']
    video_description = video['snippet']['description']
    video_publish_date = video['snippet']['publishedAt']

    print("Title:", video_title)
    print("Description:", video_description)
    print("Publish Date:", video_publish_date)
    print()
